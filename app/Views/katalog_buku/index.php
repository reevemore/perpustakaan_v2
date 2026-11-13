<?php
/**
 * Halaman Katalog Buku.
 * Data dari KatalogBuku::index():
 *   $bukuList     (buku.* + nama_kategori)
 *   $kategoriList (id_kategori, nama_kategori)
 */
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                    <h4 class="card-title mb-0">Daftar Buku</h4>
                    <button type="button" class="btn btn-warning waves-effect waves-light" id="btn-tambah">
                        <i class="mdi mdi-plus me-1"></i> Tambah Buku
                    </button>
                </div>

                <table id="tabel-buku" class="table table-bordered table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                            <th style="width:120px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bukuList as $i => $b): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($b['kode_buku']) ?></td>
                                <td><?= esc($b['judul']) ?></td>
                                <td><?= esc($b['penulis']) ?></td>
                                <td><?= esc($b['penerbit'] ?: '-') ?></td>
                                <td><?= esc($b['tahun_terbit'] ?: '-') ?></td>
                                <td><span class="badge bg-soft-info text-info"><?= esc($b['nama_kategori']) ?></span></td>
                                <td class="text-center">
                                    <?php if ((int) $b['stok'] > 0): ?>
                                        <span class="badge bg-success"><?= (int) $b['stok'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info waves-effect btn-edit"
                                        data-id="<?= esc($b['id_buku'], 'attr') ?>"
                                        data-kode="<?= esc($b['kode_buku'], 'attr') ?>"
                                        data-judul="<?= esc($b['judul'], 'attr') ?>"
                                        data-penulis="<?= esc($b['penulis'], 'attr') ?>"
                                        data-penerbit="<?= esc($b['penerbit'], 'attr') ?>"
                                        data-tahun="<?= esc($b['tahun_terbit'], 'attr') ?>"
                                        data-stok="<?= esc($b['stok'], 'attr') ?>"
                                        data-kategori="<?= esc($b['id_kategori'], 'attr') ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger waves-effect btn-hapus"
                                        data-id="<?= esc($b['id_buku'], 'attr') ?>"
                                        data-judul="<?= esc($b['judul'], 'attr') ?>">
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
<div class="modal fade" id="modal-buku" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="form-buku" method="post" action="<?= base_url('katalog-buku/store') ?>">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modal-buku-title">Tambah Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="kode_buku" class="form-label">Kode Buku</label>
                        <input type="text" class="form-control" id="kode_buku" name="kode_buku" maxlength="20"
                            placeholder="Kosongkan = otomatis">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul" name="judul" maxlength="150" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="penulis" name="penulis" maxlength="100" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control" id="penerbit" name="penerbit" maxlength="100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                        <input type="text" class="form-control" id="tahun_terbit" name="tahun_terbit"
                            maxlength="4" inputmode="numeric" placeholder="2024">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" value="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($kategoriList as $k): ?>
                                <option value="<?= esc($k['id_kategori'], 'attr') ?>"><?= esc($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
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
    var baseUrl = '<?= rtrim(base_url('katalog-buku'), '/') ?>';
    var modalEl = document.getElementById('modal-buku');
    var modal   = new bootstrap.Modal(modalEl);
    var form    = document.getElementById('form-buku');
    var title   = document.getElementById('modal-buku-title');

    $('#tabel-buku').DataTable({
        responsive: true,
        columnDefs: [{ orderable: false, targets: 8 }],
        language: {
            search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data buku',
            paginate: { previous: '&laquo;', next: '&raquo;' }
        }
    });

    // Tambah
    $('#btn-tambah').on('click', function () {
        form.reset();
        form.action = baseUrl + '/store';
        title.textContent = 'Tambah Buku';
        form.stok.value = 0;
        modal.show();
    });

    // Edit
    $('#tabel-buku').on('click', '.btn-edit', function () {
        var d = $(this).data();
        form.reset();
        form.action = baseUrl + '/update/' + d.id;
        title.textContent = 'Edit Buku';
        form.kode_buku.value    = d.kode;
        form.judul.value        = d.judul;
        form.penulis.value      = d.penulis;
        form.penerbit.value     = d.penerbit;
        form.tahun_terbit.value = d.tahun;
        form.stok.value         = d.stok;
        form.id_kategori.value  = d.kategori;
        modal.show();
    });

    // Hapus (konfirmasi SweetAlert2)
    $('#tabel-buku').on('click', '.btn-hapus', function () {
        var id = $(this).data('id'), judul = $(this).data('judul');
        Swal.fire({
            title: 'Hapus buku?',
            text: '"' + judul + '" akan dihapus.',
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