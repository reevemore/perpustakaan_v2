<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPeminjamanModel extends Model
{
    protected $table            = 'detail_peminjaman';
    protected $primaryKey       = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_peminjaman',
        'id_buku',
        'jumlah',
        'tanggal_kembali',
        'denda',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_peminjaman' => 'required|is_natural_no_zero|is_not_unique[peminjaman.id_peminjaman]',
        'id_buku'       => 'required|is_natural_no_zero|is_not_unique[buku.id_buku]',
        'jumlah'        => 'required|is_natural_no_zero',
    ];
    protected $validationMessages = [
        'id_peminjaman' => [
            'required'      => 'Transaksi peminjaman tidak valid.',
            'is_not_unique' => 'Transaksi peminjaman tidak ditemukan.',
        ],
        'id_buku' => [
            'required'      => 'Buku wajib dipilih.',
            'is_not_unique' => 'Buku yang dipilih tidak valid.',
        ],
        'jumlah' => [
            'required'            => 'Jumlah buku wajib diisi.',
            'is_natural_no_zero'  => 'Jumlah buku minimal 1.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /** Besaran denda per hari keterlambatan (rupiah). */
    private const DENDA_PER_HARI = 1000;

    /**
     * Ambil semua item buku pada satu transaksi peminjaman,
     * beserta judul & kode buku (join). Dipakai untuk detail
     * transaksi dan form proses pengembalian.
     */
    public function getByPeminjaman(int $idPeminjaman): array
    {
        return $this->select('detail_peminjaman.*, buku.judul, buku.kode_buku')
            ->join('buku', 'buku.id_buku = detail_peminjaman.id_buku')
            ->where('detail_peminjaman.id_peminjaman', $idPeminjaman)
            ->findAll();
    }

    /**
     * Ambil item-item buku yang BELUM dikembalikan (tanggal_kembali
     * masih kosong) dari satu transaksi peminjaman. Dipakai di menu
     * Pengembalian untuk menampilkan buku mana saja yang masih bisa
     * diproses pengembaliannya pada satu transaksi.
     */
    public function getBelumKembaliByPeminjaman(int $idPeminjaman): array
    {
        return $this->select('detail_peminjaman.*, buku.judul, buku.kode_buku')
            ->join('buku', 'buku.id_buku = detail_peminjaman.id_buku')
            ->where('detail_peminjaman.id_peminjaman', $idPeminjaman)
            ->where('detail_peminjaman.tanggal_kembali', null)
            ->findAll();
    }

    /**
     * Ambil satu baris detail beserta judul buku & tanggal jatuh tempo
     * dari transaksi induknya. Dipakai saat memproses pengembalian
     * satu item buku.
     */
    public function getOneWithRelasi(int $idDetail): ?array
    {
        return $this->select('detail_peminjaman.*, buku.judul, buku.kode_buku, peminjaman.tanggal_jatuh_tempo, peminjaman.id_anggota')
            ->join('buku', 'buku.id_buku = detail_peminjaman.id_buku')
            ->join('peminjaman', 'peminjaman.id_peminjaman = detail_peminjaman.id_peminjaman')
            ->where('detail_peminjaman.id_detail', $idDetail)
            ->first();
    }

    /**
     * Hitung denda keterlambatan berdasarkan selisih hari antara
     * tanggal jatuh tempo dan tanggal kembali. Jika belum telat,
     * hasilnya 0.
     */
    public function hitungDenda(string $tanggalJatuhTempo, string $tanggalKembali): float
    {
        $jatuhTempo = new \DateTime($tanggalJatuhTempo);
        $kembali    = new \DateTime($tanggalKembali);

        if ($kembali <= $jatuhTempo) {
            return 0.0;
        }

        $selisihHari = (int) $jatuhTempo->diff($kembali)->format('%a');

        return $selisihHari * self::DENDA_PER_HARI;
    }

    /**
     * Hitung total denda dari seluruh item pada satu transaksi
     * peminjaman. Dipakai untuk menampilkan total tagihan denda.
     */
    public function totalDendaByPeminjaman(int $idPeminjaman): float
    {
        $row = $this->selectSum('denda')
            ->where('id_peminjaman', $idPeminjaman)
            ->first();

        return (float) ($row['denda'] ?? 0);
    }
}