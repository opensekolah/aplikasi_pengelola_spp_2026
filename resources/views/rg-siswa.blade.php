@include('header')
@include('ruangguru')

<div class="content-area">
    <!-- start content-area -->

    <div class="mb-3">
        <h3>Pilih Kelas</h3>
    </div>

    <div class="menukotak">
        <?php foreach($data['angkatan'] as $a): ?>

        <a href="/datasiswa/angkatan/<?= $a->id ?>">
            <div class="menuitem btn btn-primary">
                <i data-lucide="users"></i>
                <h3><?= optional($a->kelompok)->name ?></h3>
                <?php if(optional($a->kelompok)->name === 'Alumni'): ?>

                <span>Tahun Lulus <?= $a->tahun_lulus->format('Y') ?></span>

                <?php else: ?>

                <span>Tahun Masuk <?= $a->name ?></span>

                <?php endif; ?>
            </div>
        </a>

        <?php endforeach; ?>

        <!--a href="/tambahangkatan">
            <div class="menuitem btn btn-success">
                <i data-lucide="plus"></i>
                
                <span>Kenaikan Kelas & Tahun Ajaran Baru</span>
            </div>
        </a-->

    </div> <!-- end menukotak -->


</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->
@include('footer')
