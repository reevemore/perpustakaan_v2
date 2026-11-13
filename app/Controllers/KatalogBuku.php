<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriBukuModel;

class KatalogBuku extends BaseController
{
    protected BukuModel $bukuModel;
    protected KategoriBukuModel $kategoriModel;

    public function __construct()
    {
        $this->bukuModel     = new BukuModel();
        $this->kategoriModel = new KategoriBukuModel();
    }

    public function index()
    {
        $data = [
            'nama_petugas' => session()->get('nama_petugas'),
            'bukuList'     => $this->bukuModel->getAllWithKategori(),
            'kategoriList' => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),

            'extraStyles'  => ['assets/extensions/sweetalert2/sweetalert2.min.css'],
            'extraScripts' => ['assets/extensions/sweetalert2/sweetalert2.min.js'],
        ];

        $data['content']    = view('katalog_buku/index', $data);
        $data['title']      = 'Katalog Buku';
        $data['activeMenu'] = 'master';
        $data['activeSub']  = 'katalog-buku';

        return view('layouts/main', $data);
    }

    public function store()
    {
        $payload = $this->buildPayload();

        // Kode buku boleh dikosongkan di form; kalau kosong, generate
        // otomatis DULU sebelum divalidasi, supaya tetap lolos aturan
        // 'required' & 'is_unique' pada BukuModel seperti biasa.
        if ($payload['kode_buku'] === '') {
            $payload['kode_buku'] = $this->bukuModel->generateKodeBuku();
        }

        if (! $this->bukuModel->validate($payload)) {
            return redirect()->to('/katalog-buku')
                ->withInput()
                ->with('errors', $this->bukuModel->errors());
        }

        $this->bukuModel->insert($payload);

        return redirect()->to('/katalog-buku')
            ->with('message', 'Buku berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $buku = $this->bukuModel->find($id);

        if (! $buku) {
            return redirect()->to('/katalog-buku')
                ->with('error', 'Data buku tidak ditemukan.');
        }

        $payload = $this->buildPayload();

        // Kalau field kode buku dikosongkan saat edit, pertahankan kode
        // yang lama (tidak digenerate ulang) supaya kode buku yang sudah
        // beredar di sirkulasi tidak berubah tanpa sengaja.
        if ($payload['kode_buku'] === '') {
            $payload['kode_buku'] = $buku['kode_buku'];
        }

        $payload['id_buku'] = $id;

        if (! $this->bukuModel->validate($payload)) {
            return redirect()->to('/katalog-buku')
                ->withInput()
                ->with('errors', $this->bukuModel->errors());
        }

        unset($payload['id_buku']);

        // Validasi sudah dilakukan manual di atas (dengan id_buku disertakan
        // supaya placeholder {id_buku} pada rule kode_buku terisi benar).
        // skipValidation(true) WAJIB di sini, karena Model::update() akan
        // menjalankan validate() lagi secara internal memakai $payload yang
        // sudah tidak berisi id_buku, sehingga placeholder-nya gagal
        // ter-replace dan is_unique salah menganggap baris yang sedang
        // diedit sebagai duplikat kode_buku.
        $this->bukuModel->skipValidation(true)->update($id, $payload);

        return redirect()->to('/katalog-buku')
            ->with('message', 'Buku berhasil diperbarui.');
    }

    /**
     * Hapus buku. Ditolak jika masih direferensikan oleh
     * detail_peminjaman (FK ON DELETE RESTRICT).
     */
    public function delete($id = null)
    {
        $buku = $this->bukuModel->find($id);

        if (! $buku) {
            return redirect()->to('/katalog-buku')
                ->with('error', 'Data buku tidak ditemukan.');
        }

        $jumlahDetail = $this->bukuModel->countDetailPeminjamanByBuku((int) $id);

        if ($jumlahDetail > 0) {
            return redirect()->to('/katalog-buku')
                ->with('error', "Buku \"{$buku['judul']}\" masih memiliki riwayat peminjaman, tidak bisa dihapus.");
        }

        $this->bukuModel->delete($id);

        return redirect()->to('/katalog-buku')
            ->with('message', 'Buku berhasil dihapus.');
    }

    /**
     * Susun payload dari POST, dipakai bareng untuk store & update.
     */
    private function buildPayload(): array
    {
        return [
            'kode_buku'    => trim((string) $this->request->getPost('kode_buku')),
            'judul'        => trim((string) $this->request->getPost('judul')),
            'penulis'      => trim((string) $this->request->getPost('penulis')),
            'penerbit'     => trim((string) $this->request->getPost('penerbit')),
            'tahun_terbit' => trim((string) $this->request->getPost('tahun_terbit')),
            'stok'         => (int) $this->request->getPost('stok'),
            'id_kategori'  => (int) $this->request->getPost('id_kategori'),
        ];
    }
}