@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<script>
    function go(id){
        document.getElementById(id).scrollIntoView({
            behavior:'smooth',
            block:'start'
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
                    <button onclick="go('laporan')" class="btn-primary glow">
                        Buat Laporan
                    </button>

                    <button onclick="go('riwayat')" class="btn-secondary">
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
                        class="map-image"
                    >

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

                    <h3>Monitoring Transparan</h3>

                    <p>
                        Pantau progres laporan secara realtime
                        mulai dari diterima hingga selesai.
                    </p>

                    <div class="progress-group">

                        <div class="progress-item">
                            <div class="progress-top">
                                <span>Diterima</span>
                                <span>100%</span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill blue"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-top">
                                <span>Diproses</span>
                                <span>75%</span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill orange"></div>
                            </div>
                        </div>

                        <div class="progress-item">
                            <div class="progress-top">
                                <span>Selesai</span>
                                <span>55%</span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill green"></div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="riwayat-preview reveal">

                @foreach($reports as $r)

                <div class="riwayat-item">

                    <div class="riwayat-top">

                        <div>
                            <b>{{ $r->alamat }}</b>

                            <small>
                                {{ $r->created_at->format('d M Y') }}
                            </small>
                        </div>

                        <div class="status {{ strtolower($r->status) }}">
                            {{ $r->status }}
                        </div>

                    </div>

                </div>

                @endforeach

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

        <div class="placeholder-box">
            <p>Section FAQ akan diisi oleh divisi terkait.</p>
        </div>

    </section>

</div>

<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection