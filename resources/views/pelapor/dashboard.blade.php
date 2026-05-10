@extends('layouts.app')

<<<<<<< HEAD
@section('title', 'Dashboard Pelapor — Serojap')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard Pelapor</h1>
                <p class="text-lg text-gray-600 mb-8">Selamat datang, {{ auth()->user()->name }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-file-document-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Laporan</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-check-circle-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Selesai</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->whereHas('statuses', function($q){
                                        $q->where('status', 'selesai');
                                    })->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-clock-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Diproses</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->whereHas('statuses', function($q){
                                        $q->where('status', 'proses');
                                    })->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Laporan Terakhir</h2>
                        <a href="{{ route('laporan.create') }}" class="bg-[#2657c1] hover:bg-[#1e4ba8] text-white px-6 py-2.5 rounded-lg font-medium transition duration-200">
                            Buat Laporan Baru
                        </a>
                    </div>
                    @php $recent = auth()->user()->laporan()->latest()->take(5)->get(); @endphp
                    @if($recent->count() > 0)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recent as $laporan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($laporan->lokasi, 40) }}</div>
                                            <div class="text-sm text-gray-500">{{ $laporan->kecamatan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $laporan->statusTerbaru?->status == 'selesai' ? 'bg-green-100 text-green-800' : ($laporan->statusTerbaru?->status == 'proses' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ $laporan->statusTerbaru?->status ?? 'baru' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $laporan->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="mdi mdi-file-document-outline text-6xl text-gray-400 mb-4 block"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada laporan</h3>
                        <p class="text-gray-500 mb-6">Mulai laporkan jalan rusak di sekitar Anda</p>
                        <a href="{{ route('laporan.create') }}" class="bg-[#2657c1] hover:bg-[#1e4ba8] text-white px-8 py-3 rounded-xl font-semibold text-lg transition duration-200 inline-block">
                            Buat Laporan Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
=======
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

<<<<<<< HEAD
@endsection
>>>>>>> 43717ca (fix workspace clean)
=======
<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection
>>>>>>> 0fb7ba8 (dashboard baru)
