<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme-bootstrap')

    <title>@yield('title', 'Beranda') &middot; SEROJAP</title>

    <meta name="description" content="@yield('deskripsi', 'Sistem Pelaporan Kerusakan Jalan Kabupaten Purwakarta. Laporkan kerusakan jalan, pantau progres penanganannya secara online.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    {{-- CSS khusus halaman. Ditaruh di head, bukan di dalam body,
         supaya tidak ada kedipan layout dan cascade-nya bisa
         diprediksi. --}}
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', 'Public Sans', sans-serif;
            margin: 0;
            padding: 0;
            /* Warna dasar halaman diambil dari token netral, bukan
               `--accent-tint`. `--accent-tint` dicampur dengan
               `--surface` supaya cocok sebagai latar badge/sorotan;
               memakainya sebagai canvas membuat seluruh halaman
              BERWARNA aksen dan, di mode dark, teks yang mewarisi
               warna awal jadi hitam di atas latar gelap.
               `color` juga ditulis di sini karena tanpa itu elemen
               tanpa `color` eksplisit mewarisi hitam. */
            background: var(--bg);
            color: var(--ink);
        }

        /* ================= FOOTER ================= */

        .footer-section {
            margin-top: 70px;
            /* Full-bleed memakai `width: 100vw` + `margin-left: -50vw`
               SEBELUMNYA, dan itu penyebab scrollbar horizontal di
               dashboard. `100vw` menghitung lebar viewport termasuk
               scrollbar vertikal, sedangkan area konten hanya
               `clientWidth`. Selisihnya sekitar 15px, dan itulah yang
               membuat halaman bisa digeser ke kanan. Karena `<footer>`
               ini anak langsung `<body>` yang margin-nya 0, `100%`
               sudah persis selebar area konten -- tanpa `vw` sama
               sekali, jadi tidak mungkin meluber. */            width: 100%;
            position: relative;
            background:
                linear-gradient(135deg, #0b1416 0%, var(--band-to) 45%, var(--band-from) 100%);
            color: #ffffff;
            overflow: hidden;
        }

        .footer-section::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -150px;
            right: -120px;
        }

        .footer-section::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            bottom: -100px;
            left: 8%;
        }

        .footer-container {
            position: relative;
            z-index: 2;
            max-width: 1180px;
            margin: 0 auto;
            padding: 46px 28px 24px;
        }

        .footer-main {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr 1fr;
            gap: 34px;
            align-items: start;
        }

        .footer-brand-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .footer-logo-box {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            flex-shrink: 0;
        }

        .footer-logo-box img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .footer-brand h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.8px;
        }

        .footer-brand .footer-subtitle {
            margin: 3px 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 500;
        }

        .footer-description {
            max-width: 470px;
            margin: 0;
            color: rgba(255, 255, 255, 0.78);
            font-size: 14px;
            line-height: 1.8;
        }

        .footer-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 18px;
        }

        .footer-badges span {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            font-weight: 700;
        }

        .footer-panel {
            padding: 20px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(12px);
        }

        .footer-panel h4 {
            margin: 0 0 14px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.4px;
        }

        .footer-panel p {
            margin: 0;
            color: rgba(255, 255, 255, 0.76);
            font-size: 13px;
            line-height: 1.7;
        }

        .footer-mini-list {
            display: grid;
            gap: 12px;
            margin-top: 15px;
        }

        .footer-mini-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .footer-mini-icon {
            width: 30px;
            height: 30px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .footer-mini-item strong {
            display: block;
            font-size: 13px;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .footer-mini-item span,
        .footer-mini-item a {
            color: rgba(255, 255, 255, 0.74);
            font-size: 12.5px;
            line-height: 1.5;
            text-decoration: none;
        }

        .footer-mini-item a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-ornament {
            display: grid;
            gap: 14px;
        }

        .footer-system-card {
            padding: 18px;
            border-radius: 22px;
            /* Kaca putih + `color: var(--ink)` berarti teks terang di
               atas putih pada mode dark. Kacanya diturunkan dari
               `--surface` supaya ikut mode. */
            background: color-mix(in srgb, var(--surface) 92%, transparent);
            color: var(--ink);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.10);
        }

        .footer-system-card span {
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            color: var(--accent);
            /* Latar 10% aksen di atas kartu membuat teks aksen kontras
               1:1. `--accent-tint` menurunkan aksen terhadap
               `--surface`, jadi selisihnya cukup di kedua mode. */
            background: var(--accent-tint);
            padding: 6px 10px;
            border-radius: 999px;
            margin-bottom: 10px;
        }

        .footer-system-card h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: var(--ink);
        }

        .footer-system-card p {
            margin: 8px 0 0;
            font-size: 12.5px;
            line-height: 1.6;
            color: var(--ink-soft);
        }

        .footer-bottom {
            margin-top: 34px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.16);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            color: rgba(255, 255, 255, 0.74);
            font-size: 12.5px;
        }

        .footer-bottom p {
            margin: 0;
        }

        .footer-bottom strong {
            color: #ffffff;
            font-weight: 700;
        }

        @media (max-width: 980px) {
            .footer-main {
                grid-template-columns: 1fr;
            }

            .footer-description {
                max-width: 100%;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 640px) {
            .footer-container {
                padding: 38px 20px 22px;
            }

            .footer-brand-head {
                align-items: flex-start;
            }

            .footer-brand h3 {
                font-size: 21px;
            }
        }
    </style>
</head>

<body>

    {{-- Flash message global: dipakai semua halaman pelapor. --}}
    @if (session('success') || session('error') || session('status'))
        <div class="container mx-auto px-4 sm:px-6 pt-4 space-y-2" aria-live="polite">
            @if (session('success'))
                <x-flash-message type="success">{{ session('success') }}</x-flash-message>
            @endif

            @if (session('error'))
                <x-flash-message type="error">{{ session('error') }}</x-flash-message>
            @endif

            @if (session('status'))
                <x-flash-message type="warning">{{ session('status') }}</x-flash-message>
            @endif
        </div>
    @endif

    <a href="#konten-utama" class="skip-link">Lompat ke konten utama</a>

    <div class="navbar">

        <a href="{{ route('dashboard') }}" class="nav-left">
            <img src="{{ asset('logo.png') }}" alt="Logo SEROJAP">
            <span>SEROJAP</span>
        </a>

        <nav class="nav-menu" aria-label="Navigasi utama">
            {{-- Menu menuju halaman sungguhan, bukan anchor ke section di
                 dashboard. Sebelumnya "Prosedur" dan "FAQ" melompat ke
                 anchor, sehingga halaman /prosedur dan /pusat-bantuan
                 yang sudah dibuat tidak pernah bisa dibuka. --}}
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('laporan.create') }}" class="nav-item {{ request()->routeIs('laporan.create') ? 'active' : '' }}">Buat Laporan</a>
            <a href="{{ route('laporan.my-report') }}" class="nav-item {{ request()->routeIs('laporan.my-report') ? 'active' : '' }}">Riwayat Saya</a>
            <a href="{{ route('prosedur') }}" class="nav-item {{ request()->routeIs('prosedur') ? 'active' : '' }}">Prosedur</a>
            <a href="{{ route('pelapor.faq') }}" class="nav-item {{ request()->routeIs('pelapor.faq') ? 'active' : '' }}">Pusat Bantuan</a>
        </nav>

        <div class="nav-actions">
            {{-- Picker tema, versi navbar. Tampil >= 640px; di bawah
                 itu versinya ada di dalam menu mobile. --}}
            @include('partials.theme-picker', ['variant' => 'nav'])

            <button type="button" class="hamburger" onclick="toggleMenu()"
                aria-label="Buka menu navigasi" aria-controls="mobileMenu" aria-expanded="false">
                <span aria-hidden="true">☰</span>
            </button>

            @auth
                  {{-- Fallback avatar memakai aset LOKAL. Sebelumnya fallback
                       ini `https://i.pravatar.cc/100`, yaitu layanan pihak
                       ketiga: setiap kunjungan halaman mengirim IP pengguna ke
                       luar, dan begitu service-nya_down atau perangkat sedang
                       offline, navbar menampilkan gambar rusak. Aset lokal yang
                       sama sudah dipakai `profile/edit.blade.php`. --}}
                  <a href="{{ route('profile.edit') }}" class="nav-profile">
                      <img src="{{ auth()->user()->foto_profil
                          ? asset('storage/' . auth()->user()->foto_profil)
                          : asset('assets/pelapor/images/avatar-1.jpg') }}"
                          alt="Foto profil {{ auth()->user()->name }}"
                          width="40" height="40" loading="lazy">


                    <span>{{ auth()->user()->name }}</span>
                </a>

                {{-- Logout. Sebelumnya satu-satunya tombol logout ada di
                     halaman profil, tidak pernah di navbar -- artinya
                     pengguna tidak bisa keluar dari halaman mana pun
                     selain profil. --}}
                <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="nav-logout">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-item">Masuk</a>
                <a href="{{ route('register') }}" class="nav-cta">Daftar</a>
            @endauth
        </div>

    </div>

    <div id="mobileMenu" class="mobile-menu">
        {{-- Picker tema, versi menu mobile: tampil < 640px, di atas
             tautan navigasi supaya panelnya membuka ke bawah tanpa
             menabrak bawah viewport. --}}
        @include('partials.theme-picker', ['variant' => 'menu'])

        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('laporan.create') }}">Buat Laporan</a>
        <a href="{{ route('laporan.my-report') }}">Riwayat Saya</a>
        <a href="{{ route('prosedur') }}">Prosedur</a>
        <a href="{{ route('pelapor.faq') }}">Pusat Bantuan</a>

        @auth
            <a href="{{ route('profile.edit') }}">Profil Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}">Masuk</a>
            <a href="{{ route('register') }}">Daftar</a>
        @endauth
    </div>

    <main id="konten-utama">

    <div class="content">
        @yield('content')
    </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="footer-section">

        <div class="footer-container">

            <div class="footer-main">

                <!-- BRAND -->
                <div class="footer-brand">

                    <div class="footer-brand-head">

                        <div class="footer-logo-box">
                            <img src="{{ asset('assets/pelapor/images/logo-serojap.webp') }}" alt="Logo SEROJAP">
                        </div>

                        <div>
                            <h3>SEROJAP</h3>
                            <p class="footer-subtitle">
                                Sistem Pelaporan Jalan Rusak Purwakarta
                            </p>
                        </div>

                    </div>

                    <p class="footer-description">
                        Platform layanan digital untuk mendukung pelaporan kerusakan jalan secara lebih tertib,
                        transparan, dan terintegrasi di wilayah Kabupaten Purwakarta.
                    </p>

                    <div class="footer-badges">
                        <span>Layanan Digital</span>
                        <span>Infrastruktur Jalan</span>
                        <span>Purwakarta</span>
                    </div>

                </div>

                <!-- INSTANSI -->
                <div class="footer-panel">

                    <h4>Instansi Pengelola</h4>

                    <p>
                        Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Purwakarta
                        sebagai unsur pendukung pengelolaan infrastruktur wilayah.
                    </p>

                    <div class="footer-mini-list">

                        <div class="footer-mini-item">
                            <div class="footer-mini-icon">📍</div>
                            <div>
                                <strong>Wilayah Layanan</strong>
                                <span>Kabupaten Purwakarta</span>
                            </div>
                        </div>

                        <div class="footer-mini-item">
                            <div class="footer-mini-icon">✉</div>
                            <div>
                                <strong>Email Informasi</strong>
                                <a href="mailto:info@serojap.purwakartakab.go.id" class="inline-block py-1">
                                    infoserojap@gmail.com
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- PAJANGAN SISTEM -->
                <div class="footer-ornament">

                    <div class="footer-system-card">
                        <span>Government Service</span>
                        <h4>Layanan Pengaduan Infrastruktur Jalan</h4>
                        <p>
                            Mendukung partisipasi masyarakat dalam menyampaikan informasi kerusakan jalan
                            secara cepat dan terdokumentasi.
                        </p>
                    </div>

                    <div class="footer-system-card">
                        <span>Integrated Report</span>
                        <h4>Monitoring Laporan Digital</h4>
                        <p>
                            Laporan masyarakat tersimpan dalam sistem sehingga proses pemantauan dapat
                            dilakukan lebih rapi dan transparan.
                        </p>
                    </div>

                </div>

            </div>

            <div class="footer-bottom">

                <p>
                    © {{ date('Y') }} <strong>SEROJAP</strong>. Sistem Pelaporan Jalan Rusak Purwakarta.
                </p>

                <p>
                    Pemerintah Kabupaten Purwakarta
                </p>

            </div>

        </div>

    </footer>

    <script src="{{ asset('js/navbar.js') }}"></script>

    {{-- Script khusus halaman (dialog, peta, dsb). --}}
    @stack('scripts')

    </body>

</html>