<?php
/**
 * Halaman Kategori Buku.
 * Data dari KategoriBuku::index(): $kategoriList (id_kategori, nama_kategori)
 */
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Kategori Buku</h4>
                    <button type="button" class="btn btn-warning waves-effect waves-light" id="btn-tambah">
                        <i class="mdi mdi-plus me-1"></i> Tambah Kategori
                    </button>
                </div>

                <table id="tabel-kategori" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:60px">No</th>
                            <th>Nama Kategori</th>
                            <th style="width:140px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kategoriList as $i => $k): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($k['nama_kategori']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info waves-effect btn-edit"
                                        data-id="<?= esc($k['id_kategori'], 'attr') ?>"
                                        data-nama="<?= esc($k['nama_kategori'], 'attr') ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger waves-effect btn-hapus"
                                        data-id="<?= esc($k['id_kategori'], 'attr') ?>"
                                        data-nama="<?= esc($k['nama_kategori'], 'attr') ?>">
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
<div class="modal fade" id="modal-kategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="form-kategori" method="post" action="<?= base_url('kategori-buku/store') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modal-kategori-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                    maxlength="100" required>
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
    var baseUrl = '<?= rtrim(base_url('kategori-buku'), '/') ?>';
    var modalEl = document.getElementById('modal-kategori');
    var modal   = new bootstrap.Modal(modalEl);
    var form    = document.getElementById('form-kategori');

    $('#tabel-kategori').DataTable({
        responsive: true,
        columnDefs: [{ orderable: false, targets: 2 }],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada kategori buku',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        }
    });

    // Tambah
    $('#btn-tambah').on('click', function () {
        form.action = baseUrl + '/store';
        document.getElementById('modal-kategori-title').textContent = 'Tambah Kategori';
        form.nama_kategori.value = '';
        modal.show();
    });

    // Edit
    $('#tabel-kategori').on('click', '.btn-edit', function () {
        form.action = baseUrl + '/update/' + $(this).data('id');
        document.getElementById('modal-kategori-title').textContent = 'Edit Kategori';
        form.nama_kategori.value = $(this).data('nama');
        modal.show();
    });

    modalEl.addEventListener('shown.bs.modal', function () { form.nama_kategori.focus(); });

    // Hapus (konfirmasi SweetAlert2)
    $('#tabel-kategori').on('click', '.btn-hapus', function () {
        var id = $(this).data('id'), nama = $(this).data('nama');
        Swal.fire({
            title: 'Hapus kategori?',
            text: 'Kategori "' + nama + '" akan dihapus.',
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