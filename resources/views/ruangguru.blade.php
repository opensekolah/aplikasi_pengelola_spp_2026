@include('header')
<div class="layoutadmin">
    <div class="a">
        <div class="logo-nav mt-3">
            <!--img class="" src="uploads/logo_nu.png"-->
            <img src="{{ asset('uploads/logo_nu.png') }}">
        </div>
        <span class="title-nav mb-3">SMP Ma'arif NU 01 Wanareja</span>

        <nav class="menubar">
            @if (session('role') == 'bendahara')
                <a href="/ruangguru">
                    <div class="menubarlist <?= $data['title'] == 'Ruang Guru' ? 'active' : '' ?>">
                        <i data-lucide="home"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
                <!--div class="separator mt-3">
                    Perencanaan :
                </div-->

                <a href="/datasiswa">
                    <div class="menubarlist <?= $data['title'] == 'Kelola Data Siswa' ? 'active' : '' ?>">
                        <i data-lucide="users"></i>
                        <span>Kelola Data Siswa</span>
                    </div>
                </a>
                <a href="/datainfaq">
                    <div class="menubarlist <?= $data['title'] == 'Kelola Data Infaq' ? 'active' : '' ?>">
                        <i data-lucide="list-checks"></i>
                        <span>Kelola Data Infaq</span>
                    </div>
                </a>
            @endif
            <!--div class="separator mt-3">
                Pengelolaan :
            </div-->

            <a href="/datapembayaran">
                <div class="menubarlist <?= $data['title'] == 'Pembayaran' ? 'active' : '' ?>">
                    <i data-lucide="calculator"></i>
                    <span>Pembayaran</span>
                </div>
            </a>
            @if (session('role') == 'bendahara')
                <a href="/datatagihan">
                    <div class="menubarlist <?= $data['title'] == 'Tagihan' ? 'active' : '' ?>">
                        <i data-lucide="file-text"></i>
                        <span>Tagihan</span>
                    </div>
                </a>

                <!--div class="separator mt-3">
                Pelaporan :
            </div>
            </a>
            <a href="">
                <div class="menubarlist <?= $data['title'] == 'Laporan Bulanan' ? 'active' : '' ?>">
                    <i data-lucide="trending-up"></i>
                    <span>Laporan Bulanan</span>
                </div>
            </a>
            <a href="">
                <div class="menubarlist <?= $data['title'] == 'Laporan Tahunan' ? 'active' : '' ?>">
                    <i data-lucide="trending-up"></i>
                    <span>Laporan Tahunan</span>
                </div>
            </a>
            <a href="">
                <div class="menubarlist <?= $data['title'] == 'Buku Induk' ? 'active' : '' ?>">
                    <i data-lucide="book"></i>
                    <span>Buku Induk</span>
                </div>
            </a>

            <div class="separator mt-3">

            </div-->
                <a href="/pengaturan">
                    <div class="menubarlist <?= $data['title'] == 'Pengaturan' ? 'active' : '' ?>">
                        <i data-lucide="settings"></i>
                        <span>Pengaturan</span>
                    </div>
                </a>
            @endif
            <a href="/keluar">
                <div class="menubarlist">
                    <i data-lucide="door-open"></i>
                    <span>Keluar</span>
                </div>
            </a>



        </nav>

        <!--div class="wa-status-box mt-auto p-3">
            <div class="d-flex align-items-center justify-content-between rounded-3 px-3 py-2"
                style="background: var(--birutua); border: none;">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i data-lucide="message-circle" style="width: 16px; height: 16px;"></i>
                    <span class="small">WhatsApp</span>
                </div>
                <span id="wa-gateway-status"
                    class="badge rounded-pill text-bg-secondary d-flex align-items-center">
                    <i data-lucide="loader-circle" id="wa-status-icon" class="spin-icon"
                        style="width: 12px; height: 12px;"></i>
                    <span id="wa-status-text"></span>
                </span>
            </div>
        </div-->

        <style>
            .spin-icon {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>

        


    </div>
    <div class="b">
        <div class="navbar">
            <span class="ms-3">Aplikasi Pengelola Infaq</span>
            <div class="profil"><img src="https://api.dicebear.com/9.x/initials/svg?seed={{ session('name') }}"
                    alt="avatar" class="rounded-circle me-2" /><span>{{ session('name') }}</span></div>
        </div>
        <div class="content">
            <div class="title-area">

                @if ($data['title'] == 'Ruang Guru')
                    <h2 class="desktop-only">{{ $data['title'] }}</h2>
                    <h2 class="text-birutua">Aplikasi Pengelola Infaq</h2>
                @else
                    <!--a href="javascript:history.back()">
                        <i data-lucide="arrow-left"></i>
                    </a-->
                    <h2>{{ $data['title'] }}</h2>
                @endif

            </div>
