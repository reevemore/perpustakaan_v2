<?php

namespace App\Models;

use CodeIgniter\Model;

class PetugasModel extends Model
{
    protected $table            = 'petugas';
    protected $primaryKey       = 'id_petugas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_petugas',
        'username',
        'password',
    ];

    // Timestamps
    protected $useTimestamps = false;

    // Validation (dipakai untuk store; untuk update, controller pakai rule custom
    // supaya password boleh dikosongkan)
    protected $validationRules = [
        'nama_petugas' => 'required|max_length[120]',
        'username'     => 'required|max_length[50]|is_unique[petugas.username,id_petugas,{id_petugas}]',
        'password'     => 'required|max_length[255]',
    ];
    protected $validationMessages = [
        'nama_petugas' => [
            'required'   => 'Nama petugas wajib diisi.',
            'max_length' => 'Nama petugas maksimal 120 karakter.',
        ],
        'username' => [
            'required'   => 'Username wajib diisi.',
            'max_length' => 'Username maksimal 50 karakter.',
            'is_unique'  => 'Username sudah dipakai petugas lain, gunakan username lain.',
        ],
        'password' => [
            'required'   => 'Password wajib diisi.',
            'max_length' => 'Password maksimal 255 karakter.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Cari petugas berdasarkan username.
     * Digunakan pada proses login.
     */
    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Hitung berapa banyak transaksi peminjaman yang tercatat atas nama petugas ini.
     * Dipakai sebelum proses hapus, karena FK peminjaman.id_petugas -> petugas
     * bersifat ON DELETE RESTRICT (lihat db_perpustakaan.sql).
     */
    public function countPeminjamanByPetugas(int $idPetugas): int
    {
        return $this->db->table('peminjaman')
            ->where('id_petugas', $idPetugas)
            ->countAllResults();
    }
}