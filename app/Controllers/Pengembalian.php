<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;
use App\Models\DetailPeminjamanModel;
use App\Models\BukuModel;

class Pengembalian extends BaseController
{
    protected PeminjamanModel $peminjamanModel;
    protected DetailPeminjamanModel $detailModel;
    protected BukuModel $bukuModel;

    public function __construct()
    {
        $this->peminjamanModel = new PeminjamanModel();
        $this->detailModel     = new DetailPeminjamanModel();
        $this->bukuModel       = new BukuModel();
    }

    /**
     * Daftar semua transaksi peminjaman yang masih aktif (status = 'dipinjam'),
     * beserta buku-buku yang belum dikembalikan pada tiap transaksi.
     * Ini halaman utama menu Pengembalian.
     */
    public function index()
    {
        $peminjamanList = $this->peminjamanModel->getBelumSelesaiWithRelasi();

        // Lampirkan daftar item buku yang belum kembali pada tiap transaksi.
        foreach ($peminjamanList as &$pinjam) {
            $pinjam['items'] = $this->detailModel->getBelumKembaliByPeminjaman((int) $pinjam['id_peminjaman']);
        }
        unset($pinjam);

        $data = [
            'nama_petugas'   => session()->get('nama_petugas'),
            'peminjamanList' => $peminjamanList,

            'extraStyles'  => ['assets/extensions/sweetalert2/sweetalert2.min.css'],
            'extraScripts' => ['assets/extensions/sweetalert2/sweetalert2.min.js'],
        ];

        $data['content']    = view('pengembalian/index', $data);
        $data['title']      = 'Transaksi Pengembalian';
        $data['activeMenu'] = 'transaksi';
        $data['activeSub']  = 'pengembalian';

        return view('layouts/main', $data);
    }

    /**
     * Proses pengembalian SATU item buku dari sebuah transaksi peminjaman.
     * - Menghitung denda otomatis jika telat dari tanggal_jatuh_tempo.
     * - Mengembalikan stok buku sesuai jumlah yang dipinjam.
     * - Jika seluruh item pada transaksi sudah kembali, status
     *   peminjaman otomatis diubah menjadi 'selesai'.
     *
     * Input opsional: tanggal_kembali (default: hari ini).
     */
    public function kembalikan($idDetail = null)
    {
        $detail = $this->detailModel->getOneWithRelasi((int) $idDetail);

        if (! $detail) {
            return redirect()->to('/pengembalian')
                ->with('error', 'Data peminjaman buku tidak ditemukan.');
        }

        if (! empty($detail['tanggal_kembali'])) {
            return redirect()->to('/pengembalian')
                ->with('error', "Buku \"{$detail['judul']}\" sudah tercatat dikembalikan sebelumnya.");
        }

        $tanggalKembali = trim((string) $this->request->getPost('tanggal_kembali'));
        $tanggalKembali = $tanggalKembali !== '' ? $tanggalKembali : date('Y-m-d');

        $denda = $this->detailModel->hitungDenda($detail['tanggal_jatuh_tempo'], $tanggalKembali);

        $db = \Config\Database::connect();
        $db->transStart();

        $this->detailModel->update($detail['id_detail'], [
            'tanggal_kembali' => $tanggalKembali,
            'denda'           => $denda,
        ]);

        // Kembalikan stok buku sesuai jumlah yang dipinjam pada item ini.
        $this->bukuModel->tambahStok((int) $detail['id_buku'], (int) $detail['jumlah']);

        // Jika seluruh item pada transaksi ini sudah kembali, tutup transaksinya.
        if ($this->peminjamanModel->semuaDetailSudahKembali((int) $detail['id_peminjaman'])) {
            $this->peminjamanModel->update($detail['id_peminjaman'], ['status' => 'selesai']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/pengembalian')
                ->with('error', 'Proses pengembalian gagal, silakan coba lagi.');
        }

        $pesan = $denda > 0
            ? "Buku \"{$detail['judul']}\" berhasil dikembalikan. Denda keterlambatan: Rp " . number_format($denda, 0, ',', '.') . '.'
            : "Buku \"{$detail['judul']}\" berhasil dikembalikan tanpa denda.";

        return redirect()->to('/pengembalian')->with('message', $pesan);
    }

    /**
     * Proses pengembalian SEMUA item buku yang belum kembali
     * dalam satu transaksi peminjaman sekaligus (dipakai jika
     * anggota mengembalikan seluruh buku pinjamannya bersamaan).
     *
     * Input opsional: tanggal_kembali (default: hari ini).
     */
    public function kembalikanSemua($idPeminjaman = null)
    {
        $peminjaman = $this->peminjamanModel->find((int) $idPeminjaman);

        if (! $peminjaman) {
            return redirect()->to('/pengembalian')
                ->with('error', 'Transaksi peminjaman tidak ditemukan.');
        }

        $items = $this->detailModel->getBelumKembaliByPeminjaman((int) $idPeminjaman);

        if (empty($items)) {
            return redirect()->to('/pengembalian')
                ->with('error', 'Semua buku pada transaksi ini sudah dikembalikan.');
        }

        $tanggalKembali = trim((string) $this->request->getPost('tanggal_kembali'));
        $tanggalKembali = $tanggalKembali !== '' ? $tanggalKembali : date('Y-m-d');

        $db = \Config\Database::connect();
        $db->transStart();

        $totalDenda = 0.0;

        foreach ($items as $item) {
            $denda = $this->detailModel->hitungDenda($peminjaman['tanggal_jatuh_tempo'], $tanggalKembali);
            $totalDenda += $denda;

            $this->detailModel->update($item['id_detail'], [
                'tanggal_kembali' => $tanggalKembali,
                'denda'           => $denda,
            ]);

            $this->bukuModel->tambahStok((int) $item['id_buku'], (int) $item['jumlah']);
        }

        $this->peminjamanModel->update($idPeminjaman, ['status' => 'selesai']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/pengembalian')
                ->with('error', 'Proses pengembalian gagal, silakan coba lagi.');
        }

        $pesan = $totalDenda > 0
            ? 'Seluruh buku berhasil dikembalikan. Total denda: Rp ' . number_format($totalDenda, 0, ',', '.') . '.'
            : 'Seluruh buku berhasil dikembalikan tanpa denda.';

        return redirect()->to('/pengembalian')->with('message', $pesan);
    }
}