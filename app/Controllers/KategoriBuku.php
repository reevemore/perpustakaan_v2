<?php

namespace App\Controllers;

use App\Models\KategoriBukuModel;

class KategoriBuku extends BaseController
{
    protected KategoriBukuModel $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriBukuModel();
    }

    /**
     * Menampilkan daftar kategori buku.
     */
    public function index()
    {
        $data = [
            'nama_petugas' => session()->get('nama_petugas'),
            'kategoriList' => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
    
            'extraStyles'  => ['assets/extensions/sweetalert2/sweetalert2.min.css'],
            'extraScripts' => ['assets/extensions/sweetalert2/sweetalert2.min.js'],
        ];

        $data['content']    = view('kategori_buku/index', $data);
        $data['title']      = 'Kategori Buku';
        $data['activeMenu'] = 'master';
        $data['activeSub']  = 'kategori-buku';

        return view('layouts/main', $data);
    }

    /**
     * Simpan kategori baru.
     */
    public function store()
    {
        $namaKategori = trim((string) $this->request->getPost('nama_kategori'));

        if (! $this->kategoriModel->validate(['nama_kategori' => $namaKategori])) {
            return redirect()->to('/kategori-buku')
                ->withInput()
                ->with('errors', $this->kategoriModel->errors());
        }

        $this->kategoriModel->insert(['nama_kategori' => $namaKategori]);

        return redirect()->to('/kategori-buku')
            ->with('message', 'Kategori buku berhasil ditambahkan.');
    }

    /**
     * Perbarui kategori yang sudah ada.
     */
    public function update($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return redirect()->to('/kategori-buku')
                ->with('error', 'Data kategori tidak ditemukan.');
        }

        $namaKategori = trim((string) $this->request->getPost('nama_kategori'));

        // Sertakan id_kategori supaya rule is_unique tahu baris mana yang dikecualikan.
        $dataToValidate = [
            'id_kategori'   => $id,
            'nama_kategori' => $namaKategori,
        ];

        if (! $this->kategoriModel->validate($dataToValidate)) {
            return redirect()->to('/kategori-buku')
                ->withInput()
                ->with('errors', $this->kategoriModel->errors());
        }

        // Validasi sudah dilakukan manual di atas (dengan id_kategori disertakan
        // supaya placeholder {id_kategori} pada rule is_unique terisi benar).
        // skipValidation(true) di sini WAJIB, karena Model::update() akan
        // menjalankan validate() lagi secara internal memakai $data yang
        // dikirim ke update() — dan array itu tidak berisi id_kategori,
        // sehingga placeholder-nya gagal ter-replace dan is_unique salah
        // menganggap baris yang sedang diedit sebagai duplikat.
        $this->kategoriModel->skipValidation(true)->update($id, ['nama_kategori' => $namaKategori]);

        return redirect()->to('/kategori-buku')
            ->with('message', 'Kategori buku berhasil diperbarui.');
    }

    /**
     * Hapus kategori.
     * Ditolak jika masih ada buku yang memakai kategori ini
     * (FK buku.id_kategori -> kategori_buku ON DELETE RESTRICT).
     */
    public function delete($id = null)
    {
        $kategori = $this->kategoriModel->find($id);

        if (! $kategori) {
            return redirect()->to('/kategori-buku')
                ->with('error', 'Data kategori tidak ditemukan.');
        }

        $jumlahBuku = $this->kategoriModel->countBukuByKategori((int) $id);

        if ($jumlahBuku > 0) {
            return redirect()->to('/kategori-buku')
                ->with('error', "Kategori \"{$kategori['nama_kategori']}\" masih dipakai oleh {$jumlahBuku} buku, tidak bisa dihapus.");
        }

        $this->kategoriModel->delete($id);

        return redirect()->to('/kategori-buku')
            ->with('message', 'Kategori buku berhasil dihapus.');
    }
}