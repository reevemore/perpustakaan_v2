        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">

                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="<?= base_url('dashboard') ?>" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url('assets/images/logo-dark.png') ?>" alt="" height="20">
                            </span>
                        </a>

                        <a href="<?= base_url('dashboard') ?>" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url('assets/images/logo-light.png') ?>" alt="" height="20">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>

                </div>

                <div class="d-flex">

                    <!-- Fullscreen -->
                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="mdi mdi-fullscreen"></i>
                        </button>
                    </div>

                    <!-- User -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="<?= base_url('assets/images/users/avatar-7.jpg') ?>"
                                alt="Avatar">
                            <span class="d-none d-xl-inline-block ms-1"><?= esc($nama_petugas ?? session()->get('nama_petugas')) ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                <i class="mdi mdi-power font-size-16 align-middle me-1 text-danger"></i> Logout
                            </a>
                        </div>
                    </div>

                    <!-- Pengaturan tema (right bar) -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                            <i class="mdi mdi-cog-outline font-size-20"></i>
                        </button>
                    </div>

                </div>
            </div>
        </header>