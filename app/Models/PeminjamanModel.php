<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id_peminjaman';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id_anggota',
        'id_petugas',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_anggota'          => 'required|is_natural_no_zero|is_not_unique[anggota.id_anggota]',
        'id_petugas'          => 'required|is_natural_no_zero|is_not_unique[petugas.id_petugas]',
        'tanggal_pinjam'      => 'required|valid_date[Y-m-d]',
        'tanggal_jatuh_tempo' => 'required|valid_date[Y-m-d]',
        'status'              => 'permit_empty|in_list[dipinjam,selesai]',
    ];
    protected $validationMessages = [
        'id_anggota' => [
            'required'      => 'Anggota wajib dipilih.',
            'is_not_unique' => 'Anggota yang dipilih tidak valid.',
        ],
        'id_petugas' => [
            'required'      => 'Petugas tidak terdeteksi, silakan login ulang.',
            'is_not_unique' => 'Petugas yang tercatat tidak valid.',
        ],
        'tanggal_pinjam' => [
            'required'   => 'Tanggal pinjam wajib diisi.',
            'valid_date' => 'Format tanggal pinjam tidak valid.',
        ],
        'tanggal_jatuh_tempo' => [
            'required'   => 'Tanggal jatuh tempo wajib diisi.',
            'valid_date' => 'Format tanggal jatuh tempo tidak valid.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua transaksi peminjaman beserta nama anggota & nama petugas (join),
     * dipakai untuk tabel daftar transaksi peminjaman/pengembalian.
     */
    public function getAllWithRelasi(): array
    {
        return $this->select('peminjaman.*, anggota.nama AS nama_anggota, anggota.kelas, petugas.nama_petugas')
            ->join('anggota', 'anggota.id_anggota = peminjaman.id_anggota')
            ->join('petugas', 'petugas.id_petugas = peminjaman.id_petugas')
            ->orderBy('peminjaman.tanggal_pinjam', 'DESC')
            ->findAll();
    }

    /**
     * Ambil satu transaksi peminjaman beserta relasi anggota & petugas.
     * Dipakai di halaman detail/proses pengembalian.
     */
    public function getOneWithRelasi(int $idPeminjaman): ?array
    {
        return $this->select('peminjaman.*, anggota.nama AS nama_anggota, anggota.kelas, petugas.nama_petugas')
            ->join('anggota', 'anggota.id_anggota = peminjaman.id_anggota')
            ->join('petugas', 'petugas.id_petugas = peminjaman.id_petugas')
            ->where('peminjaman.id_peminjaman', $idPeminjaman)
            ->first();
    }

    /**
     * Ambil semua transaksi peminjaman yang statusnya masih 'dipinjam'
     * (belum selesai / masih ada buku yang belum kembali), beserta
     * relasi anggota & petugas. Diurutkan berdasarkan tanggal jatuh
     * tempo terdekat agar transaksi yang berisiko telat tampil di atas.
     * Dipakai sebagai data utama menu Pengembalian.
     */
    public function getBelumSelesaiWithRelasi(): array
    {
        return $this->select('peminjaman.*, anggota.nama AS nama_anggota, anggota.kelas, petugas.nama_petugas')
            ->join('anggota', 'anggota.id_anggota = peminjaman.id_anggota')
            ->join('petugas', 'petugas.id_petugas = peminjaman.id_petugas')
            ->where('peminjaman.status', 'dipinjam')
            ->orderBy('peminjaman.tanggal_jatuh_tempo', 'ASC')
            ->findAll();
    }

    /**
     * Cek apakah semua item buku pada transaksi ini sudah dikembalikan
     * (tanggal_kembali di detail_peminjaman sudah terisi semua).
     * Dipakai untuk menentukan kapan status peminjaman berubah jadi 'selesai'.
     */
    public function semuaDetailSudahKembali(int $idPeminjaman): bool
    {
        $belumKembali = $this->db->table('detail_peminjaman')
            ->where('id_peminjaman', $idPeminjaman)
            ->where('tanggal_kembali', null)
            ->countAllResults();

        return $belumKembali === 0;
    }
}