<?php

namespace App\Controllers;

use App\Models\AnggotaModel;

class DataAnggota extends BaseController
{
    protected AnggotaModel $anggotaModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaModel();
    }

    public function index()
    {
        $data = [
            'nama_petugas' => session()->get('nama_petugas'),
            'anggotaList'  => $this->anggotaModel->orderBy('nama', 'ASC')->findAll(),

             'extraStyles' => ['assets/extensions/sweetalert2/sweetalert2.min.css'],
            'extraScripts' => ['assets/extensions/sweetalert2/sweetalert2.min.js'],
        ];

        $data['content']    = view('data_anggota/index', $data);
        $data['title']      = 'Data Anggota';
        $data['activeMenu'] = 'master';
        $data['activeSub']  = 'data-anggota';

        return view('layouts/main', $data);
    }

    public function store()
    {
        $payload = $this->buildPayload();

        if (! $this->anggotaModel->validate($payload)) {
            return redirect()->to('/data-anggota')
                ->withInput()
                ->with('errors', $this->anggotaModel->errors());
        }

        $this->anggotaModel->insert($payload);

        return redirect()->to('/data-anggota')
            ->with('message', 'Anggota berhasil ditambahkan.');
    }

    public function update($id = null)
    {
        $anggota = $this->anggotaModel->find($id);

        if (! $anggota) {
            return redirect()->to('/data-anggota')
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        $payload             = $this->buildPayload();
        $payload['id_anggota'] = $id;

        if (! $this->anggotaModel->validate($payload)) {
            return redirect()->to('/data-anggota')
                ->withInput()
                ->with('errors', $this->anggotaModel->errors());
        }

        unset($payload['id_anggota']);

        // Validasi sudah dilakukan manual di atas (dengan id_anggota
        // disertakan supaya placeholder {id_anggota} pada rule nis terisi
        // benar). skipValidation(true) WAJIB di sini, karena Model::update()
        // akan menjalankan validate() lagi secara internal memakai $payload
        // yang sudah tidak berisi id_anggota, sehingga placeholder-nya
        // gagal ter-replace dan is_unique salah menganggap baris yang
        // sedang diedit sebagai duplikat NIS.
        $this->anggotaModel->skipValidation(true)->update($id, $payload);

        return redirect()->to('/data-anggota')
            ->with('message', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Hapus anggota. Ditolak jika masih memiliki riwayat
     * peminjaman (FK peminjaman -> anggota ON DELETE RESTRICT).
     */
    public function delete($id = null)
    {
        $anggota = $this->anggotaModel->find($id);

        if (! $anggota) {
            return redirect()->to('/data-anggota')
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        $jumlahPeminjaman = $this->anggotaModel->countPeminjamanByAnggota((int) $id);

        if ($jumlahPeminjaman > 0) {
            return redirect()->to('/data-anggota')
                ->with('error', "Anggota \"{$anggota['nama']}\" masih memiliki riwayat peminjaman, tidak bisa dihapus.");
        }

        $this->anggotaModel->delete($id);

        return redirect()->to('/data-anggota')
            ->with('message', 'Data anggota berhasil dihapus.');
    }

    private function buildPayload(): array
    {
        return [
            'nis'    => trim((string) $this->request->getPost('nis')),
            'nama'   => trim((string) $this->request->getPost('nama')),
            'kelas'  => trim((string) $this->request->getPost('kelas')),
            'no_hp'  => trim((string) $this->request->getPost('no_hp')),
            'alamat' => trim((string) $this->request->getPost('alamat')),
        ];
    }
}