<?php

namespace App\Controllers;

use App\Models\PetugasModel;

class Auth extends BaseController
{
    protected PetugasModel $petugasModel;

    public function __construct()
    {
        $this->petugasModel = new PetugasModel();
    }

    /**
     * Menampilkan halaman login.
     * Jika sudah login, langsung arahkan ke dashboard.
     */
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Memproses submit form login.
     */
    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $petugas = $this->petugasModel->findByUsername($username);

        // Cocokkan password apa adanya (plain text) sesuai data pada database.
        if (! $petugas || $password !== $petugas['password']) {
            return redirect()->back()
                ->withInput()
                ->with('error_login', 'Username atau password salah.');
        }

        session()->set([
            'id_petugas'   => $petugas['id_petugas'],
            'nama_petugas' => $petugas['nama_petugas'],
            'username'     => $petugas['username'],
            'logged_in'    => true,
        ]);

        return redirect()->to('/dashboard');
    }

    /**
     * Logout dan hapus session.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/')->with('message', 'Anda telah logout.');
    }
}
