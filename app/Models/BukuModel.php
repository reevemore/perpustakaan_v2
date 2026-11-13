<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table            = 'buku';
    protected $primaryKey       = 'id_buku';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'id_kategori',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        // id_buku wajib punya rule sendiri karena dipakai sebagai
        // placeholder {id_buku} di rule kode_buku di bawah ini.
        'id_buku'      => 'permit_empty|is_natural_no_zero',
        'kode_buku'    => 'required|max_length[20]|is_unique[buku.kode_buku,id_buku,{id_buku}]',
        'judul'        => 'required|max_length[150]',
        'penulis'      => 'required|max_length[100]',
        'penerbit'     => 'permit_empty|max_length[100]',
        'tahun_terbit' => 'permit_empty|numeric|exact_length[4]',
        'stok'         => 'required|is_natural',
        'id_kategori'  => 'required|is_natural_no_zero|is_not_unique[kategori_buku.id_kategori]',
    ];
    protected $validationMessages = [
        'kode_buku' => [
            'required'  => 'Kode buku wajib diisi.',
            'is_unique' => 'Kode buku sudah dipakai, gunakan kode lain.',
        ],
        'judul'   => ['required' => 'Judul buku wajib diisi.'],
        'penulis' => ['required' => 'Nama penulis wajib diisi.'],
        'tahun_terbit' => [
            'numeric'      => 'Tahun terbit harus berupa angka.',
            'exact_length' => 'Tahun terbit harus 4 digit, contoh: 2024.',
        ],
        'stok' => [
            'required'    => 'Stok wajib diisi.',
            'is_natural'  => 'Stok harus berupa angka dan tidak boleh negatif.',
        ],
        'id_kategori' => [
            'required'       => 'Kategori wajib dipilih.',
            'is_not_unique'  => 'Kategori yang dipilih tidak valid.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua buku beserta nama kategorinya (join),
     * dipakai untuk tabel daftar buku.
     */
    public function getAllWithKategori(): array
    {
        return $this->select('buku.*, kategori_buku.nama_kategori')
            ->join('kategori_buku', 'kategori_buku.id_kategori = buku.id_kategori')
            ->orderBy('buku.judul', 'ASC')
            ->findAll();
    }

    /**
     * Hitung berapa banyak baris detail_peminjaman yang masih
     * mereferensikan buku ini. Dipakai sebelum hapus, karena
     * FK detail_peminjaman -> buku bersifat ON DELETE RESTRICT.
     */
    public function countDetailPeminjamanByBuku(int $idBuku): int
    {
        return $this->db->table('detail_peminjaman')
            ->where('id_buku', $idBuku)
            ->countAllResults();
    }

    /**
     * Buat kode buku otomatis dengan pola "BK" + nomor urut 3 digit
     * (BK001, BK002, dst), dipakai saat form tambah buku kode-nya
     * dikosongkan. Nomor urut diambil dari kode ber-pola "BK###" yang
     * sudah ada (bukan dari id_buku), lalu dicek ulang ke database
     * supaya tidak bentrok kalau ada kode custom yang kebetulan sama.
     */
    public function generateKodeBuku(): string
    {
        $prefix = 'BK';

        $row = $this->select("MAX(CAST(SUBSTRING(kode_buku, 3) AS UNSIGNED)) AS urut_terakhir")
            ->like('kode_buku', $prefix, 'after')
            ->first();

        $urutBerikutnya = ((int) ($row['urut_terakhir'] ?? 0)) + 1;

        do {
            $kandidat = $prefix . str_pad((string) $urutBerikutnya, 3, '0', STR_PAD_LEFT);
            $sudahDipakai = $this->where('kode_buku', $kandidat)->countAllResults() > 0;
            $urutBerikutnya++;
        } while ($sudahDipakai);

        return $kandidat;
    }

    /**
     * Kurangi stok buku sebanyak $jumlah (dipakai saat peminjaman).
     *
     * PENTING: query ini memakai ekspresi SQL mentah ('stok - x') agar
     * pengurangan stok atomik di level database. Karena kolom `stok`
     * punya validation rule `is_natural`, Model::update() akan mencoba
     * memvalidasi STRING ekspresi itu (bukan hasil akhirnya) dan SELALU
     * gagal (silent, tanpa exception) jika validasi dibiarkan aktif.
     * Makanya validasi wajib dimatikan khusus untuk query ini dengan
     * skipValidation(true) — bukan dimatikan secara global di $skipValidation.
     */
    public function kurangiStok(int $idBuku, int $jumlah): bool
    {
        if ($jumlah <= 0) {
            return false;
        }

        return $this->skipValidation(true)
            ->where('id_buku', $idBuku)
            ->where('stok >=', $jumlah) // jaga-jaga: cegah stok jadi minus akibat race condition
            ->set('stok', 'stok - ' . $jumlah, false)
            ->update();
    }

    /**
     * Tambah stok buku sebanyak $jumlah (dipakai saat pengembalian).
     * Lihat catatan pada kurangiStok() soal alasan skipValidation(true).
     */
    public function tambahStok(int $idBuku, int $jumlah): bool
    {
        if ($jumlah <= 0) {
            return false;
        }

        return $this->skipValidation(true)
            ->where('id_buku', $idBuku)
            ->set('stok', 'stok + ' . $jumlah, false)
            ->update();
    }
}