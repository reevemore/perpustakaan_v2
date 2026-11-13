<?php
/**
 * Layout utama.
 *
 * Variabel yang dikirim controller:
 *   $title        judul halaman
 *   $content      HTML isi halaman (hasil view('xxx/index'))
 *   $activeMenu   menu induk aktif   : dashboard | master | transaksi
 *   $activeSub    sub-menu aktif     : katalog-buku | kategori-buku | data-anggota | petugas | peminjaman | pengembalian
 *   $nama_petugas nama petugas yang login
 *   $extraStyles / $extraScripts  (opsional) array path asset tambahan
 */
?>
<?= $this->include('layouts/header') ?>

<body data-sidebar="dark">

    <div id="layout-wrapper">

        <?= $this->include('layouts/topbar') ?>
        <?= $this->include('layouts/sidebar') ?>

        <!-- ============ Main Content ============ -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- Judul halaman + breadcrumb -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18"><?= esc($title ?? '') ?></h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Perpustakaan</a></li>
                                        <li class="breadcrumb-item active"><?= esc($title ?? '') ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Flash message dari controller -->
                    <?php if ($msg = session()->getFlashdata('message')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i><?= esc($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($err = session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle-outline me-2"></i><?= esc($err) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($errs = session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ((array) $errs as $e): ?>
                                    <li><?= esc($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Isi halaman -->
                    <?= $content ?>

                </div>
            </div>

            <?= $this->include('layouts/footer') ?>
        </div>
        <!-- ============ End Main Content ============ -->

    </div>

    <?= $this->include('layouts/rightbar') ?>

    <!-- Overlay for right bar -->
    <div class="rightbar-overlay"></div>

    <?= $this->include('layouts/scripts') ?>

</body>

</html>