@include('header')
@include('ruangguru')
<div class="content-area">
    <!-- start content-area -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?= session('error') ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/apin/import">
        @csrf

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Pilih</th>
                    <th>Nama</th>
                    <th>Kelas Sebelumnya</th>
                    <th>No WA</th>
                </tr>
            </thead>


            <?php foreach($data['data_api'] as $i => $d): ?>
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input" name="siswa[]" value="<?= $i ?>">
                </td>
                <td><?= $d['name'] ?></td>
                <td><?= $d['kelas'] ?></td>
                <td><?= $d['phonenumber'] ?></td>
            </tr>

            <!-- kirim data hidden -->
            <input type="hidden" name="data[<?= $i ?>][name]" value="<?= $d['name'] ?>">
            <input type="hidden" name="data[<?= $i ?>][phonenumber]" value="<?= $d['phonenumber'] ?>">
            <input type="hidden" name="id_angkatan" value="<?= $data['angkatan']->id ?>">
            <?php endforeach; ?>
        </table>

        <br>
        <button type="submit" class="btn btn-primary">
            Simpan Semua
        </button>

        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
            Batal
        </button>
    </form>

</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->



@include('footer')
