@include('header')
@include('ruangguru')
<div class="content-area">

    <div class="menukotak">
        <a href="https://docs.google.com/spreadsheets/d/1GlDb-cdsMgWWpJTq8mafmPPLioJwM63Zw9IVLgVGVxg/edit?usp=sharing"
            target="_blank">
            <div class="menuitem btn btn-success">
                <i data-lucide="book"></i>
                <span>Buku Kas Umum</span>
            </div>
        </a>
        <a href="https://docs.google.com/spreadsheets/d/14OVbdQq5GRF_PkJBois5l9mue4jsT5vzeoq3rizbSWU/edit?usp=sharing"
            target="blank">
            <div class="menuitem btn btn-success">
                <i data-lucide="users"></i>
                <span>Honorarium Guru</span>
            </div>
        </a>
        <a href="https://docs.google.com/spreadsheets/d/1iLnOO5md9Zu0LA_LbgqiMUgFx7VGn4hInJ2DOWfCtr4/edit?usp=sharing"
            target="blank">
            <div class="menuitem btn btn-success">
                <i data-lucide="printer"></i>
                <span>Tanda Terima Honorarium</span>
            </div>
        </a>
        <a href="https://docs.google.com/spreadsheets/d/1lhBsh9QwXDE62YeuGeYGdSEvlLnWuvTo3x_yoBxsk4c/edit?usp=sharing"
            target="blank">
            <div class="menuitem btn btn-success">
                <i data-lucide="file-text"></i>
                <span>Pembagian Jadwal Pelajaran Guru</span>
            </div>
        </a>
        <a href="/tambahpembayaran">
            <div class="menuitem btn btn-primary">
                <i data-lucide="calculator"></i>
                <span>Tambah Pembayaran Siswa</span>
            </div>
        </a>
        <a href="/datatagihan">
            <div class="menuitem btn btn-primary">
                <i data-lucide="file-text"></i>
                <span>Tagihan Siswa</span>
            </div>
        </a>
        <a href="https://docs.google.com/spreadsheets/d/1Mq7WlYpI37Ev-lLesuXOGl41fVgBhl7MKp9EmYJkbf4/edit?usp=sharing"
            target="blank">
            <div class="menuitem btn btn-danger">
                <i data-lucide="trending-up"></i>
                <span>Tagihan Kenaikan Kelas</span>
            </div>
        </a>
        <a href="https://docs.google.com/spreadsheets/d/10ZbBmiJA9Cjv9Z1uN8LKx1Tcn6AmV2ale4xiCPCPBFo/edit?usp=sharing"
            target="blank">
            <div class="menuitem btn btn-danger">
                <i data-lucide="graduation-cap"></i>
                <span>Tagihan Alumni</span>
            </div>
        </a>
    </div> <!-- end menukotak -->



</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->
@include('footer')
