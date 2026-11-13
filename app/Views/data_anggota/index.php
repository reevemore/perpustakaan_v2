<?php
/**
 * Halaman Data Anggota.
 * Data dari DataAnggota::index(): $anggotaList (id_anggota, nis, nama, kelas, no_hp, alamat)
 */
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Anggota</h4>
                    <button type="button" class="btn btn-warning waves-effect waves-light" id="btn-tambah">
                        <i class="mdi mdi-plus me-1"></i> Tambah Anggota
                    </button>
                </div>

                <table id="tabel-anggota" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th style="width:120px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($anggotaList as $i => $a): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($a['nis']) ?></td>
                                <td><?= esc($a['nama']) ?></td>
                                <td><?= esc($a['kelas']) ?></td>
                                <td><?= esc($a['no_hp'] ?: '-') ?></td>
                                <td><?= esc($a['alamat'] ?: '-') ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info waves-effect btn-edit"
                                        data-id="<?= esc($a['id_anggota'], 'attr') ?>"
                                        data-nis="<?= esc($a['nis'], 'attr') ?>"
                                        data-nama="<?= esc($a['nama'], 'attr') ?>"
                                        data-kelas="<?= esc($a['kelas'], 'attr') ?>"
                                        data-hp="<?= esc($a['no_hp'], 'attr') ?>"
                                        data-alamat="<?= esc($a['alamat'], 'attr') ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger waves-effect btn-hapus"
                                        data-id="<?= esc($a['id_anggota'], 'attr') ?>"
                                        data-nama="<?= esc($a['nama'], 'attr') ?>">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
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
<div class="modal fade" id="modal-anggota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="form-anggota" method="post" action="<?= base_url('data-anggota/store') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modal-anggota-title">Tambah Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nis" name="nis" maxlength="20" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kelas" name="kelas" maxlength="20" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" maxlength="120" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" maxlength="20" inputmode="numeric">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                    </div>
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
    var baseUrl = '<?= rtrim(base_url('data-anggota'), '/') ?>';
    var modalEl = document.getElementById('modal-anggota');
    var modal   = new bootstrap.Modal(modalEl);
    var form    = document.getElementById('form-anggota');
    var title   = document.getElementById('modal-anggota-title');

    $('#tabel-anggota').DataTable({
        responsive: true,
        columnDefs: [{ orderable: false, targets: 6 }],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data anggota',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        }
    });

    // Tambah
    $('#btn-tambah').on('click', function () {
        form.reset();
        form.action = baseUrl + '/store';
        title.textContent = 'Tambah Anggota';
        modal.show();
    });

    // Edit
    $('#tabel-anggota').on('click', '.btn-edit', function () {
        var d = $(this).data();
        form.reset();
        form.action = baseUrl + '/update/' + d.id;
        title.textContent = 'Edit Anggota';
        form.nis.value    = d.nis;
        form.nama.value   = d.nama;
        form.kelas.value  = d.kelas;
        form.no_hp.value  = d.hp;
        form.alamat.value = d.alamat;
        modal.show();
    });

    // Hapus (konfirmasi SweetAlert2)
    $('#tabel-anggota').on('click', '.btn-hapus', function () {
        var id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus anggota?',
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