@extends('layouts.superadmin')

@section('title', 'Dashboard Super Admin — SEROJAP')

@section('content')

@php
    /*
     * Dulu halaman ini menjalankan tiga `User::where(...)->count()`
     * langsung di dalam Blade. Selain boros (tiga query ekstra di
     * setiap load), semua query itu mengabaikan akun yang sudah
     * di-nonaktifkan, sementara controller di sebelahnya sudah
     * menghitungnya dengan benar lewat `withTrashed()`. Sekarang
     * view murni menampilkan, dan angkanya tetap dari satu sumber.
     */
    $akun = $akun ?? ['total' => 0, 'aktif' => 0, 'nonaktif' => 0, 'admin' => 0, 'pelapor' => 0, 'superAdmin' => 0];
    $ringkasanLaporan = $ringkasanLaporan ?? null;
    $laporanTerbaru = $laporanTerbaru ?? collect();
    $faqTerbaru = $faqTerbaru ?? collect();
    $petugasAktif = $petugasAktif ?? 0;
@endphp

<style>
    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        background:
            linear-gradient(135deg, rgba(38,87,193,0.96), rgba(34,109,113,0.95));
        color: white;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(38, 87, 193, 0.14);
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255,255,255,0.11);
        top: -85px;
        right: -70px;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        bottom: -80px;
        left: 42%;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-top {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-end;
    }

    .hero-text {
        max-width: 660px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.22);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .hero-title {
        font-size: 30px;
        line-height: 1.18;
        font-weight: 800;
        margin: 0;
    }

    .hero-desc {
        margin-top: 12px;
        color: rgba(255,255,255,0.88);
        line-height: 1.7;
        font-size: 15px;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        width: 360px;
        flex-shrink: 0;
    }

    .hero-stat {
        border: 1px solid rgba(255,255,255,0.24);
        background: rgba(255,255,255,0.13);
        border-radius: 18px;
        padding: 15px 12px;
        text-align: center;
        backdrop-filter: blur(12px);
    }

    .hero-stat strong {
        display: block;
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
    }

    .hero-stat span {
        display: block;
        margin-top: 7px;
        font-size: 12px;
        font-weight: 700;
        color: rgba(255,255,255,0.9);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-top: 22px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 22px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        transition: 0.25s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px rgba(15,23,42,0.08);
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
    }

    .summary-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: #94a3b8;
    }

    .summary-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 8px;
    }

    .summary-desc {
        font-size: 14px;
        line-height: 1.65;
        color: #64748b;
        margin-top: 8px;
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 18px;
        margin-top: 18px;
    }

    .action-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.04);
    }

    .dark-card {
        background: #0f172a;
        color: white;
        border-radius: 24px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(15,23,42,0.08);
    }

    .dark-card::before {
        content: '';
        position: absolute;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        top: -45px;
        right: -45px;
    }

    .btn-primary-super {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 16px;
        background: #2657c1;
        color: white;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 12px 24px rgba(38,87,193,0.18);
        transition: 0.25s ease;
    }

    .btn-primary-super:hover {
        background: #1f4674;
        transform: translateY(-2px);
    }

    .btn-secondary-super {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 16px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.25s ease;
    }

    .btn-secondary-super:hover {
        background: #f8fafc;
        transform: translateY(-2px);
    }

    @media (max-width: 1100px) {
        .hero-top {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-stats {
            width: 100%;
        }

        .summary-grid,
        .action-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div>

    <!-- HERO -->
    <section class="dashboard-hero">

        <div class="hero-content">

            <div class="hero-top">

                <div class="hero-text">

                    <div class="hero-badge">
                        <i class="mdi mdi-shield-crown-outline"></i>
                        Super Admin Panel
                    </div>

                    <h1 class="hero-title">
                        Dashboard Super Admin
                    </h1>

                </div>

                <div class="hero-stats">

                    <div class="hero-stat">
                        <strong>{{ $akun['admin'] }}</strong>
                        <span>Admin</span>
                    </div>

                    <div class="hero-stat">
                        <strong>{{ $akun['pelapor'] }}</strong>
                        <span>Pelapor</span>
                    </div>

                    <div class="hero-stat">
                        <strong>{{ $akun['total'] }}</strong>
                        <span>Akun</span>
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- SUMMARY -->
    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-icon" style="background:#eff6ff; color:#2657c1;">
                <i class="mdi mdi-account-tie-outline text-2xl"></i>
            </div>

            <p class="summary-label">Kelola Admin</p>

            <h2 class="summary-title">
                {{ $akun['admin'] }} Admin
            </h2>

        </div>

        <div class="summary-card">
            <div class="summary-icon" style="background:#ecfdf5; color:#059669;">
                <i class="mdi mdi-account-group-outline text-2xl"></i>
            </div>

            <p class="summary-label">Kelola Pelapor</p>

            <h2 class="summary-title">
                {{ $akun['pelapor'] }} Pelapor
            </h2>
        </div>

    </div>

    <!-- ACTION -->
    <div class="action-grid">

        <div class="action-card">

            <div style="width:56px; height:56px; border-radius:22px; background:linear-gradient(135deg,#eff6ff,#ecfeff); color:#2657c1; display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                <i class="mdi mdi-account-cog-outline text-3xl"></i>
            </div>

            <p class="summary-label">
                Manajemen Akun Terpusat
            </p>

            <h2 style="font-size:24px; font-weight:800; color:#0f172a; margin-top:10px;">
                Kelola admin dan pelapor dari satu panel.
            </h2>

            <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:22px;">
                <a href="{{ route('superadmin.accounts.index') }}" class="btn-primary-super">
                    <i class="mdi mdi-format-list-bulleted"></i>
                    Lihat Daftar Akun
                </a>

                <a href="{{ route('superadmin.accounts.create') }}" class="btn-secondary-super">
                    <i class="mdi mdi-account-plus-outline"></i>
                    Tambah Akun
                </a>
            </div>

        </div>

        <div class="dark-card">

            <div style="position:relative; z-index:2;">
                <div style="width:50px; height:50px; border-radius:18px; background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                    <i class="mdi mdi-information-outline text-3xl"></i>
                </div>

                <p style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.16em; color:#bfdbfe;">
                    Catatan Akses
                </p>

                <h2 style="font-size:22px; font-weight:800; line-height:1.35; margin-top:10px;">
                    Registrasi admin tidak dibuka publik.
                </h2>

                <div style="height:3px; width:100%; border-radius:999px; background:linear-gradient(90deg,#2657c1,#226d71,transparent); margin:18px 0;"></div>

                <p style="font-size:14px; color:#cbd5e1; line-height:1.7;">
                    Admin dan super admin tidak dapat registrasi sendiri.
                    Pelapor tetap dapat mendaftar mandiri melalui halaman register publik.
                </p>
            </div>

        </div>

    </div>

    {{-- ================================================================
         Ringkasan laporan + aktivitas terbaru.
         Data ini sudah diambil controller, tapi belum pernah ditampilkan,
         jadi query-nya sia-sia. Sekarang dipakai.
    ================================================================ --}}
    @if ($ringkasanLaporan)
        <div class="summary-grid" style="margin-top:24px;">

            @php
                /*
                 * Angka saja, TANPA link.
                 *
                 * Versi sebelumnya membungkus tiap kartu ini dengan
                 * `route('admin.laporan.index', ...)`. Lima kartu, satu
                 * tujuan -- dan `AdminMiddleware` selalu mengembalikan
                 * super admin ke `superadmin.dashboard` begitu menyentuh
                 * URL area admin. Jadi setiap klik memantul balik ke
                 * halaman yang sama, tidak pernah membuka daftar
                 * laporan, dan tidak ada cara lain untuk super admin
                 * sampai ke sana.
                 *
                 * Super admin memang sengaja ditahan di luar area admin,
                 * jadi angka di sini bersifat laporan-baca, bukan
                 * pintasan navigasi.
                 */
                $kartu = [
                    'Total Laporan' => $ringkasanLaporan['total'],
                    'Diterima' => $ringkasanLaporan['diterima'],
                    'Diproses' => $ringkasanLaporan['diproses'],
                    'Selesai' => $ringkasanLaporan['selesai'],
                    'Ditolak' => $ringkasanLaporan['ditolak'],
                ];
            @endphp

            @foreach ($kartu as $judul => $angka)
                <div class="summary-card">
                    <p class="summary-label">{{ $judul }}</p>
                    <h2 class="summary-title">{{ $angka }}</h2>
                </div>
            @endforeach

        </div>
    @endif

    <div class="action-grid" style="margin-top:24px;">

        <div class="action-card">
            <p class="summary-label">Laporan Terbaru</p>

            @forelse ($laporanTerbaru as $laporan)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid #f1f5f9;">
                    <div style="min-width:0;">
                        <p style="margin:0; font-size:14px; font-weight:600; color:#0f172a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $laporan->alamat }}
                        </p>
                        <p style="margin:2px 0 0; font-size:12px; color:#64748b;">
                            {{ $laporan->created_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <x-status-badge :status="$laporan->latestStatus?->status" />
                </div>
            @empty
                <p style="font-size:14px; color:#64748b; margin-top:8px;">Belum ada laporan masuk.</p>
            @endforelse
        </div>

        <div class="action-card">
            <p class="summary-label">FAQ Terbaru</p>

            @forelse ($faqTerbaru as $faq)
                <div style="padding:12px 0; border-bottom:1px solid #f1f5f9;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:#0f172a;">
                        {{ $faq->pertanyaan }}
                    </p>
                    <p style="margin:2px 0 0; font-size:12px; color:#64748b;">
                        {{ $faq->admin?->name ?? 'Admin tidak tersedia' }}
                        &middot; urutan {{ $faq->urutan }}
                    </p>
                </div>
            @empty
                <p style="font-size:14px; color:#64748b; margin-top:8px;">Belum ada FAQ.</p>
            @endforelse
        </div>

    </div>

    {{-- Petugas yang belum menyentuh laporan apa pun dalam 30 hari. --}}
    @if ($petugasAktif === 0)
        <div class="dark-card" style="margin-top:24px;">
            <div style="position:relative; z-index:2;">
                <p style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.16em; color:#bfdbfe;">
                    Perhatian
                </p>
                <h2 style="font-size:20px; font-weight:800; margin-top:8px;">
                    Tidak ada admin yang aktif menangani laporan.
                </h2>
                <p style="font-size:14px; color:#cbd5e1; line-height:1.7; margin-top:10px;">
                    Tidak ada satu pun akun admin aktif yang mengubah status
                    laporan dalam 30 hari terakhir. Periksa daftar akun --
                    mungkin akunnya sudah dinonaktifkan tanpa disengaja.
                </p>
            </div>
        </div>
    @endif

</div>

@endsection