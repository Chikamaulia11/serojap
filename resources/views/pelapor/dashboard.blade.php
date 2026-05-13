@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<script>
    function go(id) {
        document.getElementById(id).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
</script>

<div class="dashboard">

    <!-- ================= HERO ================= -->
    <section class="hero reveal" id="dashboard">

        <div class="hero-bg">
            <img src="{{ asset('assets/pelapor/images/utama.png') }}">
        </div>

        <div class="hero-overlay"></div>

        <div class="floating floating-1"></div>
        <div class="floating floating-2"></div>

        <div class="hero-content">

            <div class="hero-left">

                <div class="hero-badge">
                    Sistem Pelaporan Jalan Purwakarta
                </div>

                <h1>
                    Infrastruktur Lebih Baik,
                    <span>Dimulai Dari Laporan Kamu.</span>
                </h1>

                <p>
                    SEROJAP membantu masyarakat melaporkan kerusakan jalan
                    secara cepat, modern, dan transparan demi mobilitas
                    Purwakarta yang lebih aman.
                </p>

                <div class="hero-buttons">

                    <button
                        onclick="go('laporan')"
                        class="btn-primary glow">
                        Buat Laporan
                    </button>

                    <button
                        onclick="go('riwayat')"
                        class="btn-secondary">
                        Lihat Riwayat
                    </button>

                </div>

            </div>

            <div class="hero-right reveal">

                <div class="mini-card">
                    <h3>Monitoring Aktif</h3>
                    <p>Sistem realtime pelaporan masyarakat</p>
                </div>

                <div class="mini-card">
                    <h3>Respons Cepat</h3>
                    <p>Laporan dipantau secara berkala</p>
                </div>

            </div>

        </div>

        <div class="scroll-indicator">
            <span></span>
        </div>

    </section>

    <!-- ================= MENU ================= -->
    <section class="menu-grid reveal">

        <div class="menu-card" onclick="go('prosedur')">
            <div class="menu-icon">📘</div>
            <h3>Prosedur</h3>
            <p>Pelajari alur pelaporan secara lengkap</p>
        </div>

        <div class="menu-card" onclick="go('laporan')">
            <div class="menu-icon">📍</div>
            <h3>Laporan</h3>
            <p>Laporkan kerusakan jalan dengan akurat</p>
        </div>

        <div class="menu-card" onclick="go('riwayat')">
            <div class="menu-icon">📊</div>
            <h3>Riwayat</h3>
            <p>Pantau perkembangan laporan kamu</p>
        </div>

        <div class="menu-card" onclick="go('faq')">
            <div class="menu-icon">❓</div>
            <h3>FAQ</h3>
            <p>Pertanyaan umum seputar sistem</p>
        </div>

    </section>

    <!-- ================= PROSEDUR ================= -->
    <section id="prosedur" class="section reveal">

        <div class="section-title">
            <span>Prosedur</span>
            <h2>Alur Sistem Pelaporan</h2>
        </div>

        <div class="placeholder-box">
            <p>Section prosedur akan diisi oleh divisi terkait.</p>
        </div>

    </section>

    <!-- ================= LAPORAN ================= -->
    <section id="laporan" class="section reveal">

        <div class="section-title">
            <span>Laporan</span>
            <h2>Laporkan Kerusakan Jalan</h2>
        </div>

        <div class="laporan-layout">

            <div class="laporan-left reveal">

                <div class="laporan-card glass">

                    <div class="laporan-badge">
                        Pelaporan Cepat
                    </div>

                    <h2>
                        Satu laporan kecil,
                        bisa memberi dampak besar.
                    </h2>

                    <p>
                        Bantu percepat penanganan jalan rusak
                        dengan laporan yang akurat dan lengkap.
                    </p>

                    <div class="laporan-features">

                        <div class="feature">
                            <span>✔</span>
                            Lokasi akurat
                        </div>

                        <div class="feature">
                            <span>✔</span>
                            Upload bukti foto
                        </div>

                        <div class="feature">
                            <span>✔</span>
                            Monitoring progres
                        </div>

                    </div>

                    <a href="/report" class="btn-primary big glow">
                        Buat Laporan Sekarang
                    </a>

                </div>

            </div>

            <div class="laporan-right reveal">

                <div class="hero-map-wrapper">

                    <div class="map-glow"></div>

                    <img
                        src="{{ asset('assets/pelapor/images/peta.png') }}"
                        alt="Peta Purwakarta"
                        class="map-image">

                    <div class="map-card">

                        <h3>
                            Purwakarta Monitoring Area
                        </h3>

                        <p>
                            Sistem pelaporan terintegrasi wilayah Purwakarta.
                        </p>

                        <div class="map-status">
                            <span class="dot"></span>
                            Monitoring Active
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ================= RIWAYAT ================= -->
    <section id="riwayat" class="section reveal">

        <div class="section-title">
            <span>Riwayat</span>
            <h2>Perkembangan Laporan Kamu</h2>
        </div>

        <div class="riwayat-layout">

            <div class="riwayat-info reveal">

                <div class="info-card">

                    <h3>
                        Monitoring Transparan
                    </h3>

                    <p>
                        Pantau progres laporan secara realtime
                        mulai dari diterima hingga selesai.
                    </p>

                    @php

                    $statusTerakhir = $reports->first()?->latestStatus?->status;

                    $persenDiterima = $statusTerakhir == 'diterima' ? 100 : 0;

                    $persenDiproses = $statusTerakhir == 'diproses' ? 100 : 0;

                    $persenSelesai = $statusTerakhir == 'selesai' ? 100 : 0;

                    $persenDitolak = $statusTerakhir == 'ditolak' ? 100 : 0;

                    @endphp

                    <div class="progress-group">

                        <!-- DITERIMA -->
                        <div class="progress-item">

                            <div class="progress-top">
                                <span>Diterima</span>
                                <span>{{ $persenDiterima }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div
                                    class="progress-fill blue"
                                    style="width: {{ $persenDiterima }}%;"></div>
                            </div>

                        </div>

                        <!-- DIPROSES -->
                        <div class="progress-item">

                            <div class="progress-top">
                                <span>Diproses</span>
                                <span>{{ $persenDiproses }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div
                                    class="progress-fill orange"
                                    style="width: {{ $persenDiproses }}%;"></div>
                            </div>

                        </div>

                        <!-- SELESAI -->
                        <div class="progress-item">

                            <div class="progress-top">
                                <span>Selesai</span>
                                <span>{{ $persenSelesai }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div
                                    class="progress-fill green"
                                    style="width: {{ $persenSelesai }}%;"></div>
                            </div>

                        </div>

                        <!-- DITOLAK -->
                        <div class="progress-item">

                            <div class="progress-top">
                                <span>Ditolak</span>
                                <span>{{ $persenDitolak }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div
                                    class="progress-fill red"
                                    style="width: {{ $persenDitolak }}%;"></div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="riwayat-preview reveal">

                @forelse($reports as $r)

                @php

                $status = $r->statusTerbaru->status ?? 'diterima';

                @endphp

                <div class="riwayat-item">

                    <div class="riwayat-top">

                        <div>

                            <b>
                                {{ $r->alamat }}
                            </b>

                            <small>
                                {{ $r->created_at->format('d M Y') }}
                            </small>

                        </div>

                        <div class="status {{ strtolower($status) }}">

                            {{ ucfirst($status) }}

                        </div>

                    </div>

                </div>

                @empty

                <div class="riwayat-item">

                    <div class="riwayat-top">

                        <div>

                            <b>
                                Belum ada laporan
                            </b>

                        </div>

                    </div>

                </div>

                @endforelse

                <a href="/my-report" class="btn-primary">
                    Lihat Semua Riwayat
                </a>

            </div>

        </div>

    </section>

    <!-- ================= FAQ ================= -->
    <section id="faq" class="section reveal">

        <div class="section-title">
            <span>FAQ</span>
            <h2>Pertanyaan Umum</h2>
        </div>

        <style>
            .faq-search-google-wrapper {
                display: flex;
                justify-content: center;
                margin-bottom: 40px;
                padding: 0 20px;
            }

            .google-search-box {
                display: flex;
                align-items: center;
                width: 100%;
                max-width: 584px;
                height: 48px;
                background: #fff;
                border: 1px solid #dfe1e5;
                border-radius: 24px;
                padding: 0 20px;
                transition: box-shadow 0.2s;
            }

            .google-search-box:hover,
            .google-search-box:focus-within {
                box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
                border-color: rgba(223, 225, 229, 0);
            }

            .google-search-box input {
                flex: 1;
                background: transparent;
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
                padding: 10px 0;
                font-size: 16px;
                color: #202124;
            }

            .search-icon-left {
                margin-right: 12px;
                display: flex;
                align-items: center;
            }

            .faq-card {
                background: #fff;
                border-radius: 20px;
                padding: 28px 24px;
                cursor: pointer;
                border: 1.5px solid #e8ecf0;
                transition: all 0.3s ease;
                height: 100%;
            }

            .faq-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
                border-color: #6366f1;
            }

            .faq-card .faq-question {
                font-size: 0.95rem;
                font-weight: 700;
                color: #1e293b;
                line-height: 1.5;
                text-align: center;
                margin: 0;
            }

            .faq-card .faq-answer {
                font-size: 0.85rem;
                color: #64748b;
                line-height: 1.7;
                text-align: center;
                margin-top: 14px;
                padding-top: 14px;
                border-top: 1px solid #f1f5f9;
                display: none;
            }

            .faq-card.open .faq-answer {
                display: block;
            }

            .faq-card.open {
                border-color: #6366f1;
                box-shadow: 0 8px 24px rgba(99, 102, 241, 0.1);
            }

            .btn-lihat-lainnya {
                background: white;
                border: 1.5px solid #e2e8f0;
                padding: 12px 40px;
                border-radius: 12px;
                font-weight: 600;
                color: #1e293b;
                font-size: 0.9rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-block;
                margin-top: 40px;
            }

            .btn-lihat-lainnya:hover {
                background: #1e293b;
                color: white;
            }
        </style>

        <div class="faq-search-google-wrapper">
            <div class="google-search-box">
                <div class="search-icon-left">
                    <svg focusable="false" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" width="20" height="20" fill="#9aa0a6">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5
                        6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59
                        4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6
                        0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14
                        9.5 11.99 14 9.5 14z" />
                    </svg>
                </div>
                <input type="text" id="faqSearch"
                    placeholder="Cari pertanyaan atau jawaban..."
                    autocomplete="off">
            </div>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin: 0;" id="faqGrid">
            @forelse($faqs as $index => $f)
            <div class="faq-item {{ $index >= 6 ? 'extra-faq' : '' }}"
                style="{{ $index >= 6 ? 'display:none;' : '' }} width: calc(33.333% - 14px); min-width: 260px;">
                <div class="faq-card" onclick="toggleFaq(this)">
                    <p class="faq-question">{{ $f->pertanyaan }}</p>
                    <div class="faq-answer">{{ $f->jawaban }}</div>
                </div>
            </div>
            @empty
            <div style="width:100%; text-align:center; color:#94a3b8; padding: 40px 0; font-style:italic;">
                Belum ada pertanyaan FAQ yang ditambahkan.
            </div>
            @endforelse
        </div>

        @if($faqs->count() > 3)
        <div class="text-center">
            <button class="btn-lihat-lainnya" id="btnMore" onclick="lihatLainnya()">
                Lihat Pertanyaan Lainnya
            </button>
        </div>
        @endif

    </section>

    @push('scripts')
    <script>
        // Toggle buka/tutup jawaban
        function toggleFaq(card) {
            card.classList.toggle('open');
        }

        // Tampilkan semua FAQ tersembunyi
        function lihatLainnya() {
            document.querySelectorAll('.extra-faq').forEach(el => {
                el.style.display = '';
            });
            document.getElementById('btnMore').style.display = 'none';
        }

        // Search FAQ
        document.getElementById('faqSearch').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();

            // Tampilkan semua dulu kalau sedang search
            document.querySelectorAll('.extra-faq').forEach(el => {
                el.style.display = filter ? '' : 'none';
            });
            if (filter) {
                document.getElementById('btnMore') &&
                    (document.getElementById('btnMore').style.display = 'none');
            }

            document.querySelectorAll('.faq-item').forEach(item => {
                item.style.display =
                    item.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faqSearch');

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let items = document.querySelectorAll('.faq-item');

                    items.forEach(item => {
                        let text = item.innerText.toLowerCase();
                        item.style.display = text.includes(filter) ? "" : "none";
                    });
                });
            }
        });
    </script>

    <style>
        .white-card:hover,
        .white-card:has(.collapse.show) {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(38, 87, 193, 0.15) !important;
            outline: 2px solid #2657c1;
        }

        #faqSearch:focus {
            border-color: #2657c1 !important;
            box-shadow: 0 0 0 4px rgba(38, 87, 193, 0.1) !important;
            outline: none;
        }
    </style>

</div>

<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection