<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $data = [
            'nama_petugas'   => session()->get('nama_petugas'),
            'total_buku'     => $db->table('buku')->selectSum('stok')->get()->getRow()->stok ?? 0,
            'total_judul'    => $db->table('buku')->countAllResults(),
            'total_anggota'  => $db->table('anggota')->countAllResults(),
            'total_dipinjam' => $db->table('peminjaman')->where('status', 'dipinjam')->countAllResults(),

            // FontAwesome dipakai khusus di halaman dashboard untuk icon stat card.
            'extraStyles' => ['assets/extensions/@fortawesome/fontawesome-free/css/all.min.css'],
        ];

        // Render konten halaman dashboard terlebih dahulu,
        // lalu bungkus dengan layout utama (header, sidebar, footer, dsb).
        $data['content']     = view('dashboard', $data);
        $data['title']       = 'Dashboard';
        $data['activeMenu']  = 'dashboard';

        return view('layouts/main', $data);
    }
}