<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriBukuModel extends Model
{
    protected $table            = 'kategori_buku';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nama_kategori',
    ];

    // Timestamps
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        
        'id_kategori'   => 'permit_empty|is_natural_no_zero', // ternyata ini loh rek
        'nama_kategori' => 'required|max_length[100]|is_unique[kategori_buku.nama_kategori,id_kategori,{id_kategori}]',
    ];
    protected $validationMessages = [
        'nama_kategori' => [
            'required'  => 'Nama kategori wajib diisi.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
            'is_unique' => 'Nama kategori sudah ada, gunakan nama lain.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Hitung berapa banyak buku yang masih memakai kategori ini.
     * Dipakai sebelum proses hapus, karena FK buku -> kategori_buku
     * bersifat ON DELETE RESTRICT (lihat db_perpustakaan.sql).
     */
    public function countBukuByKategori(int $idKategori): int
    {
        return $this->db->table('buku')
            ->where('id_kategori', $idKategori)
            ->countAllResults();
    }
}