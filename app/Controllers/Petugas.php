<?php

namespace App\Controllers;

use App\Models\PetugasModel;

class Petugas extends BaseController
{
    protected PetugasModel $petugasModel;

    public function __construct()
    {
        $this->petugasModel = new PetugasModel();
    }

    /**
     * Menampilkan daftar petugas.
     */
    public function index()
    {
        $data = [
            'nama_petugas' => session()->get('nama_petugas'),
            'petugasList'  => $this->petugasModel->orderBy('nama_petugas', 'ASC')->findAll(),

            'extraStyles'  => ['assets/extensions/sweetalert2/sweetalert2.min.css'],
            'extraScripts' => ['assets/extensions/sweetalert2/sweetalert2.min.js'],
        ];

        $data['content']    = view('petugas/index', $data);
        $data['title']      = 'Data Petugas';
        $data['activeMenu'] = 'master';
        $data['activeSub']  = 'petugas';

        return view('layouts/main', $data);
    }

    /**
     * Simpan petugas baru. Password wajib diisi.
     */
    public function store()
    {
        $namaPetugas = trim((string) $this->request->getPost('nama_petugas'));
        $username    = trim((string) $this->request->getPost('username'));
        $password    = (string) $this->request->getPost('password');

        $rules = [
            'nama_petugas' => 'required|max_length[120]',
            'username'     => 'required|max_length[50]|is_unique[petugas.username]',
            'password'     => 'required|max_length[255]',
        ];
        $messages = [
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

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/petugas')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->petugasModel->skipValidation(true)->insert([
            'nama_petugas' => $namaPetugas,
            'username'     => $username,
            // Password disimpan apa adanya (plain text), mengikuti pola login di Auth.php.
            'password'     => $password,
        ]);

        return redirect()->to('/petugas')
            ->with('message', 'Data petugas berhasil ditambahkan.');
    }

    /**
     * Perbarui data petugas.
     * Password boleh dikosongkan; jika kosong, password lama dipertahankan.
     */
    public function update($id = null)
    {
        $petugas = $this->petugasModel->find($id);

        if (! $petugas) {
            return redirect()->to('/petugas')
                ->with('error', 'Data petugas tidak ditemukan.');
        }

        $namaPetugas = trim((string) $this->request->getPost('nama_petugas'));
        $username    = trim((string) $this->request->getPost('username'));
        $password    = (string) $this->request->getPost('password');

        $rules = [
            'nama_petugas' => 'required|max_length[120]',
            'username'     => "required|max_length[50]|is_unique[petugas.username,id_petugas,{$id}]",
        ];
        $messages = [
            'nama_petugas' => [
                'required'   => 'Nama petugas wajib diisi.',
                'max_length' => 'Nama petugas maksimal 120 karakter.',
            ],
            'username' => [
                'required'   => 'Username wajib diisi.',
                'max_length' => 'Username maksimal 50 karakter.',
                'is_unique'  => 'Username sudah dipakai petugas lain, gunakan username lain.',
            ],
        ];

        // Password hanya divalidasi & disimpan kalau diisi ulang.
        if ($password !== '') {
            $rules['password']    = 'max_length[255]';
            $messages['password'] = [
                'max_length' => 'Password maksimal 255 karakter.',
            ];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/petugas')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'nama_petugas' => $namaPetugas,
            'username'     => $username,
        ];

        if ($password !== '') {
            $dataUpdate['password'] = $password;
        }

        $this->petugasModel->skipValidation(true)->update($id, $dataUpdate);

        // Kalau yang diedit adalah akun yang sedang login, sinkronkan nama di session.
        if ((int) $id === (int) session()->get('id_petugas')) {
            session()->set('nama_petugas', $namaPetugas);
        }

        return redirect()->to('/petugas')
            ->with('message', 'Data petugas berhasil diperbarui.');
    }

    /**
     * Hapus petugas.
     * Ditolak jika petugas masih punya riwayat peminjaman
     * (FK peminjaman.id_petugas -> petugas ON DELETE RESTRICT),
     * atau jika mencoba menghapus akun sendiri yang sedang login.
     */
    public function delete($id = null)
    {
        $petugas = $this->petugasModel->find($id);

        if (! $petugas) {
            return redirect()->to('/petugas')
                ->with('error', 'Data petugas tidak ditemukan.');
        }

        if ((int) $id === (int) session()->get('id_petugas')) {
            return redirect()->to('/petugas')
                ->with('error', 'Tidak bisa menghapus akun sendiri yang sedang login.');
        }

        $jumlahPeminjaman = $this->petugasModel->countPeminjamanByPetugas((int) $id);

        if ($jumlahPeminjaman > 0) {
            return redirect()->to('/petugas')
                ->with('error', "Petugas \"{$petugas['nama_petugas']}\" masih memiliki {$jumlahPeminjaman} transaksi peminjaman, tidak bisa dihapus.");
        }

        $this->petugasModel->delete($id);

        return redirect()->to('/petugas')
            ->with('message', 'Data petugas berhasil dihapus.');
    }
}