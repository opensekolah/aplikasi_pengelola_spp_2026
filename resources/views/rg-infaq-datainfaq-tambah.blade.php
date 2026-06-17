@include('header')
@include('ruangguru')
<div class="content-area">
    <!-- start content-area -->
    <?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?= session('error') ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/simpaninfaq">
        @csrf

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Infaq</th>
                    <th>Kelas</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>

                <?php for($i = 0; $i < 10; $i++): ?>

                <tr>
                    <td><?= $i + 1 ?></td>

                    <!-- Nama Infaq -->
                    <td>
                        <input type="text" name="name[]" class="form-control" value="<?= old('name')[$i] ?? '' ?>" placeholder="Masukkan Nama Infaq">
                    </td>

                    <!-- Kelas -->
                    <td>
                        <select name="id_kelompok[]" class="form-control">
                            <option value="">-- Pilih Kelas --</option>

                            <?php foreach($data['kelompok'] as $k): ?>
                            <?php if(in_array($k->id, [2,3,4])): ?>
                            <option value="<?= $k->id ?>"
                                <?= (old('id_kelompok')[$i] ?? '') == $k->id ? 'selected' : '' ?>>
                                <?= $k->name ?>
                            </option>
                            <?php endif; ?>
                            <?php endforeach; ?>

                        </select>
                    </td>

                    <!-- Nominal -->
                    <td>
                        <input type="number" name="harga[]" class="form-control" placeholder="Masukkan Harga"
                            value="<?= old('harga')[$i] ?? '' ?>">
                    </td>

                </tr>

                <?php endfor; ?>

            </tbody>
        </table>

        <input type="hidden" name="id_tahunajaran" value="<?= $data['tahunajaran']->id ?>">


        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Simpan Semua
            </button>

            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                Batal
            </button>
        </div>

    </form>

</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->



@include('footer')
