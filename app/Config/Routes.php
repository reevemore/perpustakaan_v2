<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/*
 * ------------------------------------------------------------------
 * AUTH (tanpa filter, bisa diakses tanpa login)
 * ------------------------------------------------------------------
 */
$routes->get('/', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

/*
 * ------------------------------------------------------------------
 * AREA PETUGAS (wajib login -> filter 'auth')
 * ------------------------------------------------------------------
 */
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Kategori Buku
    $routes->get('kategori-buku', 'KategoriBuku::index');
    $routes->post('kategori-buku/store', 'KategoriBuku::store');
    $routes->post('kategori-buku/update/(:num)', 'KategoriBuku::update/$1');
    $routes->post('kategori-buku/delete/(:num)', 'KategoriBuku::delete/$1');

    // Katalog Buku
    $routes->get('katalog-buku', 'KatalogBuku::index');
    $routes->post('katalog-buku/store', 'KatalogBuku::store');
    $routes->post('katalog-buku/update/(:num)', 'KatalogBuku::update/$1');
    $routes->post('katalog-buku/delete/(:num)', 'KatalogBuku::delete/$1');

    // Data Anggota
    $routes->get('data-anggota', 'DataAnggota::index');
    $routes->post('data-anggota/store', 'DataAnggota::store');
    $routes->post('data-anggota/update/(:num)', 'DataAnggota::update/$1');
    $routes->post('data-anggota/delete/(:num)', 'DataAnggota::delete/$1');

    // Data Petugas
    $routes->get('petugas', 'Petugas::index');
    $routes->post('petugas/store', 'Petugas::store');
    $routes->post('petugas/update/(:num)', 'Petugas::update/$1');
    $routes->post('petugas/delete/(:num)', 'Petugas::delete/$1');

    // STEP berikutnya (Peminjaman, Pengembalian)
    // ditambahkan di dalam group ini.
});