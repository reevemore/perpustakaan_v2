<?php
/**
 * Halaman Data Petugas.
 * Data dari Petugas::index(): $petugasList (id_petugas, nama_petugas, username, password)
 * Password TIDAK ditampilkan di tabel.
 */
$idLogin = (int) session()->get('id_petugas');
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Petugas</h4>
                    <button type="button" class="btn btn-warning waves-effect waves-light" id="btn-tambah">
                        <i class="mdi mdi-plus me-1"></i> Tambah Petugas
                    </button>
                </div>

                <table id="tabel-petugas" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama Petugas</th>
                            <th>Username</th>
                            <th style="width:120px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($petugasList as $i => $p): ?>
                            <?php $isMe = (int) $p['id_petugas'] === $idLogin; ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?= esc($p['nama_petugas']) ?>
                                    <?php if ($isMe): ?>
                                        <span class="badge bg-soft-success text-success ms-1">Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($p['username']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info waves-effect btn-edit"
                                        data-id="<?= esc($p['id_petugas'], 'attr') ?>"
                                        data-nama="<?= esc($p['nama_petugas'], 'attr') ?>"
                                        data-username="<?= esc($p['username'], 'attr') ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <?php if ($isMe): ?>
                                        <button type="button" class="btn btn-sm btn-danger" disabled
                                            title="Tidak bisa menghapus akun sendiri">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-danger waves-effect btn-hapus"
                                            data-id="<?= esc($p['id_petugas'], 'attr') ?>"
                                            data-nama="<?= esc($p['nama_petugas'], 'attr') ?>">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit -->
<div class="modal fade" id="modal-petugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="form-petugas" method="post" action="<?= base_url('petugas/store') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modal-petugas-title">Tambah Petugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nama_petugas" class="form-label">Nama Petugas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_petugas" name="nama_petugas" maxlength="120" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" maxlength="50"
                        autocomplete="off" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger" id="pw-required">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" maxlength="255"
                        autocomplete="new-password" required>
                    <div class="form-text d-none" id="pw-hint">Kosongkan jika tidak ingin mengubah password.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning waves-effect waves-light">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Form tersembunyi untuk hapus -->
<form id="form-hapus" method="post" action="">
    <?= csrf_field() ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var baseUrl = '<?= rtrim(base_url('petugas'), '/') ?>';
    var modalEl = document.getElementById('modal-petugas');
    var modal   = new bootstrap.Modal(modalEl);
    var form    = document.getElementById('form-petugas');
    var title   = document.getElementById('modal-petugas-title');
    var pwHint  = document.getElementById('pw-hint');
    var pwStar  = document.getElementById('pw-required');

    $('#tabel-petugas').DataTable({
        responsive: true,
        columnDefs: [{ orderable: false, targets: 3 }],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data petugas',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        }
    });

    // Tambah: password wajib
    $('#btn-tambah').on('click', function () {
        form.reset();
        form.action = baseUrl + '/store';
        title.textContent = 'Tambah Petugas';
        form.password.required = true;
        pwHint.classList.add('d-none');
        pwStar.classList.remove('d-none');
        modal.show();
    });

    // Edit: password opsional
    $('#tabel-petugas').on('click', '.btn-edit', function () {
        var d = $(this).data();
        form.reset();
        form.action = baseUrl + '/update/' + d.id;
        title.textContent = 'Edit Petugas';
        form.nama_petugas.value = d.nama;
        form.username.value     = d.username;
        form.password.required  = false;
        pwHint.classList.remove('d-none');
        pwStar.classList.add('d-none');
        modal.show();
    });

    // Hapus (konfirmasi SweetAlert2)
    $('#tabel-petugas').on('click', '.btn-hapus', function () {
        var id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus petugas?',
            text: '"' + nama + '" akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f46a6a',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (r.isConfirmed) {
                var f = document.getElementById('form-hapus');
                f.action = baseUrl + '/delete/' + id;
                f.submit();
            }
        });
    });
});
</script>