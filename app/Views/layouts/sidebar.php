<?php
$activeMenu = $activeMenu ?? '';
$activeSub  = $activeSub ?? '';
?>
        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <div class="user-sidebar text-center">
                    <div class="dropdown">
                        <div class="user-img">
                            <img src="<?= base_url('assets/images/users/avatar-7.jpg') ?>" alt="" class="rounded-circle">
                            <span class="avatar-online bg-success"></span>
                        </div>
                        <div class="user-info">
                            <h5 class="mt-3 font-size-16 text-white"><?= esc($nama_petugas ?? session()->get('nama_petugas')) ?></h5>
                            <span class="font-size-13 text-white-50">Petugas Perpustakaan</span>
                        </div>
                    </div>
                </div>

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <ul class="metismenu list-unstyled" id="side-menu">

                        <li class="menu-title">Menu</li>

                        <!-- Dashboard -->
                        <li class="<?= $activeMenu === 'dashboard' ? 'mm-active' : '' ?>">
                            <a href="<?= base_url('dashboard') ?>" class="waves-effect <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
                                <i class="dripicons-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <!-- Master Data -->
                        <li class="<?= $activeMenu === 'master' ? 'mm-active' : '' ?>">
                            <a href="javascript: void(0);" class="has-arrow waves-effect <?= $activeMenu === 'master' ? 'active' : '' ?>">
                                <i class="dripicons-archive"></i>
                                <span>Master Data</span>
                            </a>
                            <ul class="sub-menu <?= $activeMenu === 'master' ? 'mm-show' : '' ?>" aria-expanded="<?= $activeMenu === 'master' ? 'true' : 'false' ?>">
                                <li><a href="<?= base_url('katalog-buku') ?>" class="<?= $activeSub === 'katalog-buku' ? 'active' : '' ?>">Katalog Buku</a></li>
                                <li><a href="<?= base_url('kategori-buku') ?>" class="<?= $activeSub === 'kategori-buku' ? 'active' : '' ?>">Kategori Buku</a></li>
                                <li><a href="<?= base_url('data-anggota') ?>" class="<?= $activeSub === 'data-anggota' ? 'active' : '' ?>">Data Anggota</a></li>
                                <li><a href="<?= base_url('petugas') ?>" class="<?= $activeSub === 'petugas' ? 'active' : '' ?>">Data Petugas</a></li>
                            </ul>
                        </li>

                        <!-- Transaksi -->
                        <li class="<?= $activeMenu === 'transaksi' ? 'mm-active' : '' ?>">
                            <a href="javascript: void(0);" class="has-arrow waves-effect <?= $activeMenu === 'transaksi' ? 'active' : '' ?>">
                                <i class="dripicons-swap"></i>
                                <span>Transaksi</span>
                            </a>
                            <ul class="sub-menu <?= $activeMenu === 'transaksi' ? 'mm-show' : '' ?>" aria-expanded="<?= $activeMenu === 'transaksi' ? 'true' : 'false' ?>">
                                <li><a href="<?= base_url('peminjaman') ?>" class="<?= $activeSub === 'peminjaman' ? 'active' : '' ?>">Peminjaman</a></li>
                                <li><a href="<?= base_url('pengembalian') ?>" class="<?= $activeSub === 'pengembalian' ? 'active' : '' ?>">Pengembalian</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->