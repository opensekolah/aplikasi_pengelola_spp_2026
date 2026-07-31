@include('header')
@include('ruangguru')
<div class="content-area">
    <!-- start content-area -->
    

    <blockquote class="blockquote p-3 bg-light border-start border-4 border-primary rounded">

        <p class="mb-2">
            Siswa-siswi <strong><?= optional($data['angkatan']->kelompok)->name ?></strong> ini adalah Angkatan
            
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <?= $data['angkatan']->name ?>
            </span>
        </p>
        <p class="mb-2">
            Jumlah siswa : <span class="badge bg-success rounded-pill px-3 py-2">
        <?= $data['angkatan']->siswa_count ?> Siswa
    </span>
        </p>

    </blockquote>


    <div class="mb-3">
        <a href="/tambahsiswa/<?= $data['angkatan']->id ?>" class="btn btn-primary">
            <i data-lucide="user-plus"></i> Tambah Siswa
        </a>
        <!--a href="/spmb/<?= $data['angkatan']->id ?>" class="btn btn-outline-primary">
            <i data-lucide="user-plus"></i> Ambil Data Dari SPMB
        </a>
        <a href="/apin/<?= $data['angkatan']->id ?>" class="btn btn-outline-primary">
            <i data-lucide="user-plus"></i> Ambil Data Dari Aplikasi Infaq Lama
        </a-->
        
        
    </div>


    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nomor Whatsapp</th>
                <!--th>Username</th>
                <th>Password</th-->
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data['siswa']) > 0): ?>

            <?php $no = 1; ?>
            <?php foreach($data['siswa'] as $s): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $s->name ?></td>
                <td><?= $s->no_whatsapp ?></td>
                <td>
                    <button onclick="window.location.href='/editsiswa/<?= $s->id ?>'"
                        class="btn btn-sm btn-outline-primary" title="Edit"><i data-lucide="edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                        data-id="<?= $s->id ?>" title="Hapus"><i data-lucide="trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>

            <tr>
                <td colspan="4" class="text-center text-muted">
                    Data siswa belum ada
                </td>
            </tr>

            <?php endif; ?>

        </tbody>
    </table>

</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->


<!-- modal popup -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <form id="formHapus" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    var modalHapus = document.getElementById('confirmDeleteModal');

    modalHapus.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');

        var form = document.getElementById('formHapus');
        form.action = '/hapussiswa/' + id;
    });
</script>

@include('footer')
