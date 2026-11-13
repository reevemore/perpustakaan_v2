<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8" />
    <title>Login - Perpustakaan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Aplikasi Perpustakaan" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <!-- Bootstrap Css -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="<?= base_url('assets/css/app.min.css') ?>" id="app-style" rel="stylesheet" type="text/css" />

</head>


<body class="authentication-bg bg-warning">
    <div class="home-center">
        <div class="home-desc-center">

            <div class="container">

                <div class="home-btn"><a href="<?= base_url('/') ?>" class="text-white router-link-active"><i
                            class="fas fa-home h2"></i></a></div>


                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="px-2 py-3">


                                    <div class="text-center">
                                        <a href="<?= base_url('/') ?>">
                                            <img src="<?= base_url('assets/images/perpus.png') ?>" height="130" alt="logo">
                                        </a>
                                    </div>

                                    <?php
                                    // Flash message: login gagal / belum login / logout sukses / validasi
                                    $errorLogin = session()->getFlashdata('error_login');
                                    $message    = session()->getFlashdata('message');
                                    $errors     = session()->getFlashdata('errors');
                                    ?>

                                    <?php if ($errorLogin): ?>
                                        <div class="alert alert-danger mt-4 mb-0" role="alert">
                                            <?= esc($errorLogin) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($message): ?>
                                        <div class="alert alert-success mt-4 mb-0" role="alert">
                                            <?= esc($message) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($errors): ?>
                                        <div class="alert alert-danger mt-4 mb-0" role="alert">
                                            <ul class="mb-0 ps-3">
                                                <?php foreach ((array) $errors as $e): ?>
                                                    <li><?= esc($e) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form class="form-horizontal mt-4 pt-2" action="<?= base_url('login') ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="mb-3">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="<?= esc(old('username')) ?>" placeholder="Masukkan username"
                                                autocomplete="username" autofocus required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="userpassword">Password</label>
                                            <input type="password" class="form-control" id="userpassword" name="password"
                                                placeholder="Masukkan password" autocomplete="current-password" required>
                                        </div>

                                        <div>
                                            <button class="btn btn-warning w-100 waves-effect waves-light"
                                                type="submit">Log In</button>
                                        </div>

                                    </form>


                                </div>
                            </div>
                        </div>

                        <div class="mt-5 text-center text-white">
                            <p>© <script>document.write(new Date().getFullYear())</script> Perpustakaan. <i class="mdi mdi-book-open-page-variant"></i> Sistem Informasi Perpustakaan</p>
                        </div>
                    </div>
                </div>

            </div>


        </div>
        <!-- End Log In page -->
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/node-waves/waves.min.js') ?>"></script>

    <script src="<?= base_url('assets/js/app.js') ?>"></script>

</body>

</html>