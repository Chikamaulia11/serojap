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
<!-- 
                <div class="hero-buttons">

                    <button
                        onclick="go('laporan')"
                        class="btn-primary glow"
                    >
                        Buat Laporan
                    </button>

                    <button
                        onclick="go('riwayat')"
                        class="btn-secondary"
                    >
                        Lihat Riwayat
                    </button>

                </div> -->

            </div>

            <div class="hero-right reveal">

                <div class="mini-card">
                    <h3>Monitoring Active</h3>
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

    <!-- ================= PROSEDUR ================= -->
    <section id="prosedur" class="section reveal">

        <div class="section-title">
            <span>Prosedur</span>
            <h2>Alur Sistem Pelaporan</h2>
        </div>

        <div class="prosedur-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 30px; margin-top: 30px; padding: 0 20px;">
            
            <div class="prosedur-image" style="width: 100%; max-width: 500px;">
                <img 
                    src="{{ asset('assets/pelapor/images/alur.jpeg') }}" 
                    alt="Alur Sistem Pelaporan" 
                    style="width: 100%; height: auto; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"
                >
            </div>

            <div class="prosedur-text" style="width: 100%; max-width: 800px; text-align: left;">
                <p style="font-size: 17px; line-height: 1.7; color: #475569; margin-bottom: 25px; text-align: center;">
                    Berikut adalah alur lengkap sistem pelaporan kerusakan jalan di Purwakarta, mulai dari laporan dikirim hingga penanganan selesai. Alur ini memastikan setiap laporan dipantau dan ditindaklanjuti dengan cepat dan transparan.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #075985;">
                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 600; color: #0f172a;">1. User submits report</h4>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Pengguna mengirimkan laporan lengkap dengan lokasi, jenis kerusakan, dan foto pendukung melalui sistem.</p>
                    </div>

                    <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #075985;">
                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 600; color: #0f172a;">3. Report processed</h4>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Tim melakukan verifikasi, pengecekan lokasi, dan indentifikasi pengaduan.</p>
                    </div>

                    <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #075985;">
                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 600; color: #0f172a;">2. Report received</h4>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Laporan masuk, tercatat dalam sistem, dan pelapor dapat langsung memantau status awal aduan tersebut melalui menu Riwayat.</p>
                    </div>

                    <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #075985;">
                        <h4 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 600; color: #0f172a;">4. Work completed</h4>
                        <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Perbaikan selesai dilaksanakan, pengguna bisa melihat foto hasilnya langsung di menu Riwayat.</p>
                    </div>

                </div>
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

                    <a href="/report" class="btn-primary big glow" style="display: inline-block; padding: 12px 28px; background-color: #0d9488; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 15px; border-radius: 12px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2); transition: all 0.3s ease; text-align: center;" onmouseover="this.style.backgroundColor='#0f766e'; this.style.transform='translateY(-2px)';" onmouseout="this.style.backgroundColor='#0d9488'; this.style.transform='translateY(0)';">
                        Buat Laporan Aduan
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
                                    style="width: {{ $persenDiterima }}%;"
                                ></div>
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
                                    style="width: {{ $persenDiproses }}%;"
                                ></div>
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
                                    style="width: {{ $persenSelesai }}%;"
                                ></div>
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
                                    style="width: {{ $persenDitolak }}%;"
                                ></div>
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
                background-color: transparent;
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
        </style>

        <div class="faq-search-google-wrapper">
            <div class="google-search-box">
                <div class="search-icon-left">
                    <svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="#9aa0a6">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                    </svg>
                </div>
                <input type="text" id="faqSearch" placeholder="Cari pertanyaan atau jawaban..." autocomplete="off">
            </div>
        </div>

        <div class="row g-4" id="faqGrid">
            @forelse($faqs as $f)
            <div class="col-md-6 col-lg-4 faq-item">
                <div class="card h-100 white-card shadow-sm border-0"
                    style="background: #fff; border-radius: 20px; cursor: pointer; transition: all 0.3s ease;"
                    data-bs-toggle="collapse"
                    data-bs-target="#ans{{ $f->id_faq }}">

                    <div class="card-body text-center p-4">
                        <h6 class="fw-bold mb-0" style="color: #1e293b; line-height: 1.5;">
                            {{ $f->pertanyaan }}
                        </h6>

                        <div id="ans{{ $f->id_faq }}" class="collapse mt-3 text-start">
                            <hr style="opacity: 0.1; margin: 15px 0;">
                            <p class="small text-muted mb-0" style="line-height: 1.6;">
                                {{ $f->jawaban }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted italic py-5">
                Belum ada pertanyaan FAQ yang ditambahkan.
            </div>
            @endforelse
        </div>

    </section>

<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection