<?php
/**
 * Isi halaman Dashboard.
 * Data dari Dashboard::index(): $nama_petugas, $total_buku (jumlah stok),
 * $total_judul, $total_anggota, $total_dipinjam.
 */
?>

<!-- Sambutan -->
<div class="row">
    <div class="col-12">
        <div class="card bg-soft-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Selamat datang, <?= esc($nama_petugas) ?>!</h5>
                        <p class="text-muted mb-0">Berikut ringkasan data perpustakaan saat ini.</p>
                    </div>
                    <div class="d-none d-sm-block">
                        <i class="mdi mdi-book-open-page-variant font-size-24 text-warning" style="font-size:48px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kartu statistik -->
<div class="row">

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Stok Buku</p>
                        <h4 class="mb-0"><?= number_format((int) $total_buku, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                            <i class="mdi mdi-book-multiple"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Judul Buku</p>
                        <h4 class="mb-0"><?= number_format((int) $total_judul, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-info text-info rounded-circle font-size-24">
                            <i class="mdi mdi-bookshelf"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Anggota</p>
                        <h4 class="mb-0"><?= number_format((int) $total_anggota, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-success text-success rounded-circle font-size-24">
                            <i class="mdi mdi-account-group"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Sedang Dipinjam</p>
                        <h4 class="mb-0"><?= number_format((int) $total_dipinjam, 0, ',', '.') ?></h4>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-soft-warning text-warning rounded-circle font-size-24">
                            <i class="mdi mdi-book-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Akses cepat -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Akses Cepat</h5>
                <a href="<?= base_url('peminjaman') ?>" class="btn btn-warning waves-effect waves-light me-2 mb-2">
                    <i class="mdi mdi-book-arrow-right me-1"></i> Peminjaman Baru
                </a>
                <a href="<?= base_url('pengembalian') ?>" class="btn btn-outline-warning waves-effect me-2 mb-2">
                    <i class="mdi mdi-keyboard-return me-1"></i> Pengembalian
                </a>
                <a href="<?= base_url('katalog-buku') ?>" class="btn btn-outline-secondary waves-effect mb-2">
                    <i class="mdi mdi-bookshelf me-1"></i> Katalog Buku
                </a>
            </div>
        </div>
    </div>
</div>