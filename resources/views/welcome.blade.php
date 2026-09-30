<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />

    @include('partials.favicon')
    <title>Serojap - Pelaporan Jalan Rusak Purwakarta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Serojap adalah sistem pelaporan jalan rusak Kabupaten Purwakarta. Laporkan kerusakan jalan, pantau status penanganan, dan lihat statistik perbaikan secara terbuka.">

    {{-- Script anti-FOUC harus SESUDAH tag <meta> lengkap, bukan di
         tengah-tengahnya.

         Dulu `@include` ini duduk di antara `name="description"` dan
         `content="...">`, jadi tag `<meta>`-nya tidak pernah tertutup
         waktu include itu dievaluasi. Akibatnya di DOM benar-benar:

           1. Isi script jadi TEKS VISIBEL di dalam `<body>` --
              1039 karakter, mulai `(function () { var ACCENTS = ...`,
              tinggi 138px di paling atas halaman. Itulah yang
              terbaca sebagai "teks function aneh".
           2. `<html>` TIDAK pernah dapat `data-accent`/`data-mode`,
              jadi anti-FOUC tidak berjalan sama sekali di halaman ini.

         Audit tidak pernah menangkapnya karena `theme-audit-inject.js`
         men-set sendiri atribut tema sebelum mengukur, jadi DOM rusak
         tadi tidak terlihat dari sisi audit -- hanya dari browser. --}}
    @include('partials.theme-bootstrap')

    <link rel="stylesheet" href="{{ asset('assets/pelapor/css/index.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal: var(--accent);
            --teal-dark: var(--accent-deep);
            --teal-soft: color-mix(in srgb, var(--accent) 10%, transparent);
        }

        body {
            overflow-x: hidden;
            background-color: var(--bg);
        }

        /* =========================
           HERO
        ========================= */
        .hero-section {
            padding-top: 3rem !important;
            padding-bottom: 2rem !important;
            position: relative;
            z-index: 1;
            /* `min-height: 85vh` DIHAPUS.
             *
             * Hero dipaksa setinggi 85% tinggi jendela, padahal isinya
             * cuma 538px: gambar 480px + bingkai, dan kolom teksnya
             * lebih pendek. Dengan `align-items: center` sisanya dibagi
             * rata ke atas dan bawah, jadi di 1280x900 ada 227px
             * kosong di dalam hero dan 99px lagi sebelum kartu
             * statistik -- 326px tanpa isi, dan itu yang terbaca sebagai
             * "area kosong tinggi" di screenshot penuh.
             *
             * Di 375px tinggi sebenarnya 1165px (dua kolom jadi beruntai),
             * jadi `min-height` tidak pernah mengikat di mobile --
             * masalahnya hanya di lebar desktop/tablet.
             *
             * Gambarnya sendiri tidak bermasalah: `naturalWidth` 896 dan
             * `complete` true, jadi ini bukan aset gagal dimuat yang
             * menyisakan tempat kosong. */
            display: flex;
            align-items: center;
            /* WAJIB: `.gradient-bg` adalah dekorasi 600x600px yang digeser
               ke `left: -100px`, jadi ujungnya berada di 500px. Tanpa
               pemotongan di sini, elemen itu ikut lebarkan
               `scrollWidth` dokumen: di layar 375px (iPhone SE) halaman
               jadi bisa di-scroll ke samping dan seluruh konten geser
               keluar layar.

               Dipakai `clip`, bukan `hidden`. `hidden` di satu sumbu
               memaksa sumbu lain jadi `auto`, jadi hero jadi wadah
               scroll dan panel tema yang terbuka ke bawah ikut terpotong
               di batasnya -- di 1280px panel di navbar landing page
               tidak terlihat sama sekali. `overflow-x: clip` +
               `overflow-y: visible` dip honored, jadi dekorasi
               tetap terpotong horizontal sementara panel bebas
               keluar vertikal. */
            overflow-x: clip;
            overflow-y: visible;
        }

        .gradient-bg {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, color-mix(in srgb, var(--accent) 12%, transparent) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .img-frame {
            background-color: var(--bg);
            padding: 15px;
            border-radius: 50px;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--line);
            display: inline-block;
            width: 100%;
        }

        .img-hero {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: 40px;
        }

        .btn-custom-action {
            background-color: var(--accent-tint) !important;
            color: var(--ink-soft) !important;
            border: 1px solid var(--accent-tint) !important;
            transition: all 0.2s ease-in-out !important;
            padding: 1rem 2.5rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        @media (min-width: 768px) {
            .btn-custom-action {
                width: auto;
            }
        }

        .btn-custom-action:hover {
            background-color: var(--accent-tint) !important;
            border-color: var(--accent) !important;
            color: var(--accent) !important;
        }

        .btn-custom-action:active {
            background-color: var(--accent) !important;
            color: var(--on-accent) !important;
            transform: scale(0.96);
        }

        .dot-green {
            width: 10px;
            height: 10px;
            background-color: var(--accent);
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .text-teal {
            color: var(--accent) !important;
        }

        .bg-teal-light {
            background-color: color-mix(in srgb, var(--accent) 10%, transparent) !important;
        }

        .btn-teal-solid {
            background-color: var(--accent) !important;
            color: var(--on-accent) !important;
            border: 1px solid var(--accent) !important;
            transition: all 0.2s ease-in-out !important;
            padding: 1rem 2.5rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-teal-solid:hover {
            background-color: var(--teal-dark) !important;
            border-color: var(--teal-dark) !important;
            color: #ffffff !important;
        }

        .btn-teal-outline {
            background-color: var(--surface) !important;
            color: var(--accent) !important;
            border: 1px solid var(--accent) !important;
            transition: all 0.2s ease-in-out !important;
            padding: 1rem 2.5rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-teal-outline:hover {
            background-color: var(--accent) !important;
            color: var(--on-accent) !important;
        }

        /* =========================
           SECTION UMUM
        ========================= */
        .section-serojap {
            padding: 4.5rem 0;
        }

        @media (max-width: 767px) {
            .section-serojap {
                padding: 3rem 0;
            }
        }

        .section-eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            /* Tint aksen tetap di latar, tapi TEKSnya bukan warna aksen.
             *
             * Aksen sebagai teks di atas tint-nya sendiri selalu di
             * bawah ambang: rose dark 3.99, amber light 3.78. Warna
             * aksen memang tidak dimaksudkan sebagai tinta; teks 11px di atas
             * pil 10% bukan tempat memakainya. `--ink` lolos >10:1 di kedua mode
             * dan identitas aksen tetap terbawa oleh tint-nya. */
            color: var(--ink);
            background-color: var(--teal-soft);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            margin-bottom: 1rem;
        }

        .section-title {
            font-weight: 800;
            color: var(--ink);
            line-height: 1.25;
        }

        /* =========================
           STAT BAR
        ========================= */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 1.75rem 1.5rem;
            height: 100%;
            transition: all 0.2s ease-in-out;
        }

        .stat-card:hover {
            border-color: var(--accent-tint);
            box-shadow: 0 18px 40px -24px color-mix(in srgb, var(--accent) 45%, transparent);
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.1;
        }

        .stat-suffix {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--ink-soft);
        }

        /* =========================
           CARA KERJA
        ========================= */
        .langkah-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 2rem 1.5rem;
            height: 100%;
            position: relative;
        }

        .langkah-angka {
            width: 48px;
            height: 48px;
            border-radius: 50px;
            background-color: var(--accent);
            color: var(--on-accent);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.1rem;
        }

        /* =========================
           LAPORAN PUBLIK
        ========================= */
        .laporan-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .laporan-ikon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background-color: var(--teal-soft);
            color: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* =========================
           FAQ
        ========================= */
        .faq-item {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 0 1.5rem;
        }

        .faq-item summary {
            cursor: pointer;
            list-style: none;
            font-weight: 700;
            color: var(--ink);
            padding: 1.15rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-item summary::after {
            content: '+';
            flex-shrink: 0;
            font-size: 1.4rem;
            font-weight: 400;
            color: var(--teal);
            line-height: 1;
        }

        .faq-item[open] summary::after {
            content: '\2212';
        }

        .faq-item[open] {
            border-color: var(--accent-tint);
        }

        .faq-jawaban {
            color: var(--ink-soft);
            padding-bottom: 1.25rem;
            margin-bottom: 0;
        }

        /* =========================
           CTA PENUTUP
        ========================= */
        .cta-banner {
            background: linear-gradient(135deg, var(--band-from) 0%, var(--band-to) 100%);
            border-radius: 40px;
            padding: 3.5rem 2rem;
            color: var(--on-band);
        }

        .cta-banner .section-title {
            color: var(--on-band);
        }
    </style>
</head>

<body class="bg-white">

    <main class="hero-section">
        <div class="gradient-bg"></div>

        <div class="container">
            <div class="row align-items-center gy-5">

                <div class="col-lg-6">
                    <div
                        class="d-inline-flex align-items-center bg-teal-light px-4 py-2 border border-teal-light border-opacity-25 rounded-pill mb-4">
                        <span class="dot-green"></span>
                        <small class="fw-bold text-uppercase tracking-wider" style="font-size: 10px; color: var(--ink);">Sistem Pelaporan
                            Purwakarta</small>
                    </div>

                    <h1 class="display-3 fw-bold mb-3 text-dark" style="line-height: 1.2;">
                        Sistem Pelaporan <br>
                        <span class="text-teal italic">Jalan Rusak</span> <br>
                        Purwakarta
                    </h1>

                    <p class="lead text-muted mb-4" style="font-size: 1.15rem; max-width: 500px;">
                        Satu platform terintegrasi untuk masyarakat melaporkan kerusakan jalan demi infrastruktur
                        Purwakarta yang lebih baik.
                    </p>

                    <div class="d-flex flex-column flex-md-row gap-3">
                        <a href="{{ route('register') }}" class="btn btn-custom-action">
                            Register
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-custom-action">
                            Log In
                        </a>

                        {{-- Picker tema di area tombol. Halaman ini tidak
                             punya navbar, dan tidak punya menu mobile
                             juga, jadi satu penempatan ini dipakai di
                             semua lebar. --}}
                        @include('partials.theme-picker', ['variant' => 'hero'])
                    </div>

                    <p class="text-muted mt-4 mb-0" style="font-size: 0.9rem;">
                        <span class="dot-green"></span>
                        Tidak perlu login untuk melihat statistik dan laporan publik di halaman ini.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="img-frame">
                        <img src="{{ asset('assets/pelapor/images/jalan-purwakarta.webp') }}"
                            alt="Jalan Purwakarta"
                            class="img-hero">
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- =========================
         STATISTIK PUBLIK
    ========================= --}}
    <section class="section-serojap pt-0">
        <div class="container">
            <div class="row g-4">

                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-label">Total Laporan</div>
                        <div class="stat-value">{{ number_format($totalLaporan, 0, ',', '.') }}</div>
                        <div class="stat-suffix">laporan masuk</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-label">Sedang Diproses</div>
                        <div class="stat-value">{{ number_format($jumlahPerStatus['diproses'], 0, ',', '.') }}</div>
                        <div class="stat-suffix">dalam penanganan</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-label">Selesai Diperbaiki</div>
                        <div class="stat-value">{{ number_format($jumlahPerStatus['selesai'], 0, ',', '.') }}</div>
                        <div class="stat-suffix">sudah tuntas</div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-label">Rata-rata penanganan</div>
                        <div class="stat-value">
                            {{ $rataRataHariPenanganan === null ? '-' : number_format($rataRataHariPenanganan, 1, ',', '.') }}
                        </div>
                        <div class="stat-suffix">hari</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================
         CARA KERJA
    ========================= --}}
    <section class="section-serojap bg-light">
        <div class="container">

            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-eyebrow">Cara Kerja</span>
                    <h2 class="section-title display-6">Empat Langkah, Laporan Langsung Tertangani</h2>
                    <p class="text-muted mt-3 mb-0">
                        Tidak perlu bingung. Ikuti empat langkah singkat berikut, lalu pantau progresnya sampai jalan
                        diperbaiki.
                    </p>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="langkah-card">
                        <div class="langkah-angka">1</div>
                        <h3 class="h5 fw-bold mb-2">Ambil foto & titik lokasi</h3>
                        <p class="text-muted mb-0">
                            Foto kondisi jalan dan tandai titik lokasi kerusakan langsung lewat peta agar petugas
                            mudah menemukan posisinya.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="langkah-card">
                        <div class="langkah-angka">2</div>
                        <h3 class="h5 fw-bold mb-2">Isi keterangan</h3>
                        <p class="text-muted mb-0">
                            Tuliskan lokasi, jenis kerusakan, dan tingkat keparahan yang Anda lihat agar laporan tidak
                            disalahartikan.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="langkah-card">
                        <div class="langkah-angka">3</div>
                        <h3 class="h5 fw-bold mb-2">Diverifikasi petugas</h3>
                        <p class="text-muted mb-0">
                            Admin memeriksa laporan, memberi status awal, lalu menjadwalkannya ke tim penanganan
                            jalan.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="langkah-card">
                        <div class="langkah-angka">4</div>
                        <h3 class="h5 fw-bold mb-2">Pantau status sampai selesai</h3>
                        <p class="text-muted mb-0">
                            Lihat perkembangannya di riwayat laporan Anda, termasuk catatan dan foto perbaikan dari
                            petugas.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================
         LAPORAN PUBLIK TERBARU
    ========================= --}}
    <section class="section-serojap">
        <div class="container">

            <div class="row align-items-end mb-5">
                <div class="col-lg-8">
                    <span class="section-eyebrow">Laporan Publik</span>
                    <h2 class="section-title display-6 mb-0">Laporan yang Terbaru Masuk</h2>
                    <p class="text-muted mt-3 mb-0">
                        Data pelapor yang bersifat pribadi tidak ditampilkan. Yang bisa Anda lihat di sini hanya
                        lokasi, keterangan, dan status penanganannya.
                    </p>
                </div>
            </div>

            @if ($laporanTerbaru->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Belum ada laporan yang masuk. Jadilah yang pertama melapor!</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($laporanTerbaru as $laporan)
                        @php $status = $laporan->latestStatus->status ?? null; @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="laporan-card">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                    <div class="laporan-ikon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="M4 21 L8 3" />
                                            <path d="M20 21 L16 3" />
                                            <path d="M9 8 h6" />
                                            <path d="M7.5 12 h9" />
                                            <path d="M6 16 h12" />
                                            <path d="M4.5 20 h15" />
                                        </svg>
                                    </div>
                                    <x-status-badge :status="$status" />
                                </div>

                                <h3 class="h6 fw-bold mb-2">{{ $laporan->alamat }}</h3>

                                <p class="text-muted mb-3" style="font-size: 0.92rem;">
                                    {{ $laporan->keterangan_pendek }}
                                </p>

                                <div class="mt-auto d-flex align-items-center justify-content-between text-muted"
                                    style="font-size: 0.82rem;">
                                    <span>
                                        <span class="dot-green"></span>
                                        Dilaporkan {{ $laporan->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

    {{-- =========================
         FAQ
    ========================= --}}
    <section class="section-serojap bg-light">
        <div class="container">

            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <span class="section-eyebrow">FAQ</span>
                    <h2 class="section-title display-6">Pertanyaan yang Sering Diajukan</h2>
                    <p class="text-muted mt-3 mb-0">
                        Belum menemukan jawabannya? Buka menu masuk ke akun Anda untuk melihat panduan lengkap.
                    </p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($faq as $item)
                            <details class="faq-item">
                                <summary>{{ $item->pertanyaan }}</summary>
                                <p class="faq-jawaban mb-0">{{ $item->jawaban }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================
         CTA PENUTUP
    ========================= --}}
    <section class="section-serojap">
        <div class="container">
            <div class="cta-banner text-center">
                <h2 class="section-title display-6 mb-3">Sudah Lihat Kerusakan Jalan di Sekitar Anda?</h2>
                <p class="mb-4" style="opacity: 0.9; max-width: 640px; margin-left: auto; margin-right: auto;">
                    Daftar sebagai pelapor, kirim laporan dengan foto dan titik lokasi, lalu pantau sendiri
                    penanganannya sampai selesai diperbaiki.
                </p>
                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-lg fw-bold rounded-pill px-4"
                        style="background-color:var(--surface); color:var(--accent-ink); border:1px solid var(--line);">
                        Daftar &amp; Lapor
                    </a>
                    <a href="{{ route('login') }}"
                        class="btn btn-lg fw-bold rounded-pill px-4"
                        style="background-color:transparent; color:var(--on-band); border:1px solid color-mix(in srgb, var(--on-band) 70%, transparent);">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="container py-4 border-top border-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <p class="small text-muted mb-0">© 2026 Serojap Purwakarta</p>
            <a class="small text-muted mb-0 py-1">infoserojap@gmail.com</a>
        </div>
    </footer>

    @if(session('account_deleted'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Akun Tidak Ditemukan',
                    text: "{{ session('account_deleted') }}",
                    confirmButtonText: 'Kembali ke Beranda',
                    confirmButtonColor: 'var(--accent)',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });
        </script>
    @endif

    <script src="{{ asset('assets/pelapor/js/index.js') }}" defer></script>

    </body>

</html>
