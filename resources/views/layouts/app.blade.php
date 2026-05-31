<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SEROJAP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    <style>
        body {
            font-family: 'Inter', 'Public Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7fb;
        }
    </style>
</head>

<body>

    <div class="navbar">

        <a href="{{ route('dashboard') }}#dashboard" class="nav-left">
            <img src="{{ asset('logo.png') }}">
            <span>SEROJAP</span>
        </a>

        <div class="nav-menu">
            <a href="{{ route('dashboard') }}#dashboard" class="nav-item">Dashboard</a>
            <a href="{{ route('dashboard') }}#prosedur" class="nav-item">Prosedur</a>
            <a href="{{ route('dashboard') }}#laporan" class="nav-item">Laporan</a>
            <a href="{{ route('dashboard') }}#riwayat" class="nav-item">Riwayat</a>
            <a href="{{ route('dashboard') }}#faq" class="nav-item">FAQ</a>
        </div>

        <div style="display:flex; align-items:center; gap:15px;">
            <div class="hamburger" onclick="toggleMenu()">☰</div>

            @if(auth()->check())
                <a href="{{ route('profile.edit') }}" class="nav-profile">
                    <img src="{{ auth()->user()->foto_profil
                        ? asset('storage/' . auth()->user()->foto_profil)
                        : 'https://i.pravatar.cc/100' }}">

                    <span>{{ auth()->user()->name }}</span>
                </a>
            @endif
        </div>

    </div>

    <div id="mobileMenu" class="mobile-menu">
        <a href="{{ route('dashboard') }}#dashboard">Dashboard</a>
        <a href="{{ route('dashboard') }}#prosedur">Prosedur</a>
        <a href="{{ route('dashboard') }}#laporan">Laporan</a>
        <a href="{{ route('dashboard') }}#riwayat">Riwayat</a>
        <a href="{{ route('dashboard') }}#faq">FAQ</a>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <!-- ================= FOOTER ================= -->
    <footer class="footer-section">

        <div class="footer-container">

            <div class="footer-top">

                <!-- BRAND / INSTANSI -->
                <div class="footer-brand">

                    <div class="footer-brand-head">
                        <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}" alt="Logo SEROJAP">

                        <div>
                            <h3>SEROJAP</h3>
                            <p>Sistem Pelaporan Jalan Rusak Purwakarta</p>
                        </div>
                    </div>

                    <p class="footer-description">
                        SEROJAP merupakan layanan pelaporan kerusakan jalan berbasis digital
                        untuk mendukung penanganan infrastruktur jalan secara lebih cepat,
                        transparan, dan terintegrasi di wilayah Kabupaten Purwakarta.
                    </p>

                    <div class="footer-badges">
                        <span>Transparan</span>
                        <span>Terintegrasi</span>
                        <span>Responsif</span>
                    </div>

                </div>

                <!-- LAYANAN -->
                <div class="footer-column">

                    <h4>Layanan</h4>

                    <a href="{{ route('dashboard') }}#laporan">
                        Buat Laporan
                    </a>

                    <a href="{{ route('dashboard') }}#riwayat">
                        Riwayat Laporan
                    </a>

                    <a href="{{ route('dashboard') }}#prosedur">
                        Prosedur Pelaporan
                    </a>

                    <a href="{{ route('dashboard') }}#faq">
                        Pusat Bantuan
                    </a>

                </div>

                <!-- INFORMASI -->
                <div class="footer-column">

                    <h4>Informasi Situs</h4>

                    <a href="{{ route('dashboard') }}#dashboard">
                        Dashboard
                    </a>

                    <a href="{{ route('dashboard') }}#prosedur">
                        Alur Sistem
                    </a>

                    <a href="{{ route('dashboard') }}#faq">
                        FAQ
                    </a>

                    @if(auth()->check())
                        <a href="{{ route('profile.edit') }}">
                            Profil Pengguna
                        </a>
                    @endif

                </div>

                <!-- KONTAK INSTANSI -->
                <div class="footer-column footer-contact">

                    <h4>Kontak Instansi</h4>

                    <div class="footer-contact-item">
                        <span>📍</span>
                        <p>
                            Dinas Pekerjaan Umum dan Penataan Ruang<br>
                            Kabupaten Purwakarta
                        </p>
                    </div>

                    <div class="footer-contact-item">
                        <span>✉</span>
                        <p>
                            <a href="mailto:info@serojap.purwakartakab.go.id">
                                info@serojap.purwakartakab.go.id
                            </a>
                        </p>
                    </div>

                    <div class="footer-contact-item">
                        <span>🕘</span>
                        <p>
                            Layanan sistem tersedia untuk pelaporan masyarakat
                            secara daring.
                        </p>
                    </div>

                </div>

            </div>

            <div class="footer-middle">

                <div>
                    <strong>Layanan Pengaduan Infrastruktur Jalan</strong>
                    <p>
                        Membantu masyarakat menyampaikan laporan kerusakan jalan
                        agar dapat dipantau dan ditindaklanjuti oleh pihak terkait.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}#laporan" class="footer-report-btn">
                    Laporkan Kerusakan Jalan
                </a>

            </div>

            <div class="footer-bottom">

                <p>
                    © {{ date('Y') }} SEROJAP - Sistem Pelaporan Jalan Rusak Purwakarta.
                </p>

                <p>
                    Pemerintah Kabupaten Purwakarta
                </p>

            </div>

        </div>

    </footer>

    <script src="{{ asset('js/navbar.js') }}"></script>

</body>

</html>