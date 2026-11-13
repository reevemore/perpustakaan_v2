<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table            = 'anggota';
    protected $primaryKey       = 'id_anggota';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'nis',
        'nama',
        'kelas',
        'no_hp',
        'alamat',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        // id_anggota wajib punya rule sendiri karena dipakai sebagai
        // placeholder {id_anggota} di rule nis di bawah ini.
        'id_anggota' => 'permit_empty|is_natural_no_zero',
        'nis'   => 'required|max_length[20]|is_unique[anggota.nis,id_anggota,{id_anggota}]',
        'nama'  => 'required|max_length[120]',
        'kelas' => 'required|max_length[20]',
        'no_hp' => 'permit_empty|numeric|max_length[20]',
        'alamat' => 'permit_empty',
    ];
    protected $validationMessages = [
        'nis' => [
            'required'  => 'NIS wajib diisi.',
            'is_unique' => 'NIS sudah terdaftar, gunakan NIS lain.',
        ],
        'nama'  => ['required' => 'Nama anggota wajib diisi.'],
        'kelas' => ['required' => 'Kelas wajib diisi.'],
        'no_hp' => ['numeric' => 'No. HP hanya boleh berisi angka.'],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Hitung berapa banyak baris peminjaman yang masih
     * mereferensikan anggota ini. Dipakai sebelum hapus, karena
     * FK peminjaman -> anggota bersifat ON DELETE RESTRICT.
     */
    public function countPeminjamanByAnggota(int $idAnggota): int
    {
        return $this->db->table('peminjaman')
            ->where('id_anggota', $idAnggota)
            ->countAllResults();
    }
}