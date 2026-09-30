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
          /* Awalnya gradasi 96% -> 95% biru admin; selisihnya tak
             terlihat, jadi diratakan jadi satu warna aksen pekat. */
          background: color-mix(in srgb, var(--accent) 96%, transparent);
          color: var(--on-accent);
        padding: 30px;
        box-shadow: 0 20px 40px color-mix(in srgb, var(--accent) 14%, transparent);
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
        /* Veil kaca sengaja TIDAK ditokenkan.
         *
         * Hero memakai `color-mix(... 96%, transparent)` di atas
         * `--accent`, jadi latarnya berbalik: gelap di mode light,
         * TERANG di mode dark (aksen dark memang terang). Album
         * "kaca" selalu menambah terang, jadi putih bekerja di
         * kedua mode: di light lebih terang di atas hero gelap,
         * di dark lebih terang di atas hero terang. Yang tidak bisa
         * di-hardcode adalah WARNANYA (lihat `.hero-stat span`),
         * karena teks hero ikut mode. */
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
        /* 0.88 -> 0.92, sama alasan dan sama nilainya dengan
         * `.hero-stat span` supaya hierarki teks hero konsisten. */
        color: color-mix(in srgb, var(--on-accent) 92%, transparent);
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
        /* `rgba(255,255,255,0.9)` di sini adalah satu-satunya teks di
         * dalam hero yang tidak mewarisi `color: var(--on-accent)`.
         * Di mode dark hero-nya terang, jadi putih 90% di atasnya
         * cuma ~2.4 : 1 -- label "Admin"/"Pelapor"/"Akun" nyaris
         * tak terbaca sementara angka `<strong>` di atasnya normal,
         * karena itu yang memang mewarisi.
         *
         *enting: rasionya ditentukan chip `.hero-stat` (kaca putih
         * 13%), bukan hero. Chip itu selalu lebih terang dari hero,
         * jadi teks tinta 92% di atasnya naik ke 5.27 - 7.10 : 1
         * di mode dark -- lolos di 6 aksen, `tools/audit_theme.py`
         * 0 kegagalan hero untuk dark.
         *
         * TIDAK ikut diperbaiki: mode light masih 3.55 - 4.44 : 1
         * (teal 3.88, green 3.55, purple 4.28, rose 4.34, badge
         * green 3.70 / teal 3.99). Structurally sama seperti kartu
         * gelap: chip putih 13% di atas hero gelap justru
         * MENDEKATI teks putih, jadi alpha tidak akan menolong --
         * butuh veil gelap di light dan terang di dark. Separate. */
        color: color-mix(in srgb, var(--on-accent) 92%, transparent);
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
        /* `background: white` diganti `--surface`.
         *
         * Tulisan dan border sudah pakai token, tapi latarnya tetap putih.
         * Di mode dark kartu jadi putih dengan teks terang: rasio 1.12:1,
         * jadi isinya nyaris tak terlihat. `white` justru satu-satunya
         * warna yang tidak ikut berubah saat tema diganti. */
        background: var(--surface);
        border: 1px solid var(--line);
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
        color: var(--ink-mute);
    }

    .summary-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--ink);
        margin-top: 8px;
    }

    .summary-desc {
        font-size: 14px;
        line-height: 1.65;
        color: var(--ink-soft);
        margin-top: 8px;
    }

    .action-grid {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 18px;
        margin-top: 18px;
    }

    .action-card {
        /* `background: white` diganti `--surface`.
         *
         * Tulisan dan border sudah pakai token, tapi latarnya tetap putih.
         * Di mode dark kartu jadi putih dengan teks terang: rasio 1.12:1,
         * jadi isinya nyaris tak terlihat. `white` justru satu-satunya
         * warna yang tidak ikut berubah saat tema diganti. */
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.04);
    }

    .dark-card {
        /* `background: var(--ink)` diganti token pita.
         *
         * `--ink` justru yang berbalik saat mode berubah: gelap di
         * light, `#eef3f2` di dark. Di mode dark kartu notice ini
         * jadi satu panel putih terang di tengah dashboard gelap,
         * 13 : 1 dari canvas -- persis elemen yang terlihat lupa
         * di-remap.
         *
         * Pita (`--band-to` + `--on-band`) adalah satu-satunya grub
         * di sistem tema yang dijamin gelap di kedua mode, jadi
         * {latar gelap, teks putih} benar tanpa blok tambahan per
         * mode. Lihat catatan "KARTU GELAP" di `theme.css`.
         *
         * Dipakai untuk "Catatan Akses" DAN "Perhatian" di bawah. */
        background: var(--band-to);
        color: var(--on-band);
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
          background: var(--accent);
          color: var(--on-accent);
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 12px 24px color-mix(in srgb, var(--accent) 18%, transparent);
        transition: 0.25s ease;
    }

    .btn-primary-super:hover {
        background: var(--accent-deep);
        transform: translateY(-2px);
    }

    .btn-secondary-super {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 16px;
        /* `background: white` diganti `--surface`.
         *
         * Tulisan dan border sudah pakai token, tapi latarnya tetap putih.
         * Di mode dark kartu jadi putih dengan teks terang: rasio 1.12:1,
         * jadi isinya nyaris tak terlihat. `white` justru satu-satunya
         * warna yang tidak ikut berubah saat tema diganti. */
        background: var(--surface);
        border: 1px solid var(--line);
        color: var(--ink);
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.25s ease;
    }

    .btn-secondary-super:hover {
        background: var(--bg);
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
            {{-- `--accent-tint`/`--accent` diganti pasangan chip di
                 `theme.css`: di mode dark tint-nya cuma 1.00 - 1.10 : 1
                 dari kartu, jadi bentuk lingkarannya hilang. --}}
            <div class="summary-icon" style="background:var(--icon-chip); color:var(--on-icon-chip);">
                <i class="mdi mdi-account-tie-outline text-2xl"></i>
            </div>

            <p class="summary-label">Kelola Admin</p>

            <h2 class="summary-title">
                {{ $akun['admin'] }} Admin
            </h2>

        </div>

        <div class="summary-card">
            <div class="summary-icon" style="background:var(--icon-chip-selesai); color:var(--on-icon-chip-selesai);">
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

              <div style="width:56px; height:56px; border-radius:22px; background:var(--icon-chip); color:var(--on-icon-chip); display:flex; align-items:center; justify-content:center; margin-bottom:18px;">
                <i class="mdi mdi-account-cog-outline text-3xl"></i>
            </div>

            <p class="summary-label">
                Manajemen Akun Terpusat
            </p>

            <h2 style="font-size:24px; font-weight:800; color:var(--ink); margin-top:10px;">
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

                <p style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.16em; color:var(--on-band);">
                    Catatan Akses
                </p>

                <h2 style="font-size:22px; font-weight:800; line-height:1.35; margin-top:10px;">
                    Registrasi admin tidak dibuka publik.
                </h2>

                <div style="height:3px; width:100%; border-radius:999px; background:linear-gradient(90deg,var(--band-from),var(--band-to),transparent); margin:18px 0;"></div>

                {{-- `--line` dan `--accent-tint` DILARANG di dalam kartu gelap:
                     keduanya token mode, jadi di mode dark justru gelap dan
                     rasionya di atas pita gelap jatuh ke ~1.04 : 1. Di light
                     keduanya praktis putih seperti `--on-band`, jadi tidak ada
                     yang berubah di sana. --}}
                <p style="font-size:14px; color:var(--on-band); line-height:1.7;">
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
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid var(--bg);">
                    <div style="min-width:0;">
                        <p style="margin:0; font-size:14px; font-weight:600; color:var(--ink); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $laporan->alamat }}
                        </p>
                        <p style="margin:2px 0 0; font-size:12px; color:var(--ink-soft);">
                            {{ $laporan->created_at?->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <x-status-badge :status="$laporan->latestStatus?->status" />
                </div>
            @empty
                <p style="font-size:14px; color:var(--ink-soft); margin-top:8px;">Belum ada laporan masuk.</p>
            @endforelse
        </div>

        <div class="action-card">
            <p class="summary-label">FAQ Terbaru</p>

            @forelse ($faqTerbaru as $faq)
                <div style="padding:12px 0; border-bottom:1px solid var(--bg);">
                    <p style="margin:0; font-size:14px; font-weight:600; color:var(--ink);">
                        {{ $faq->pertanyaan }}
                    </p>
                    <p style="margin:2px 0 0; font-size:12px; color:var(--ink-soft);">
                        {{ $faq->admin?->name ?? 'Admin tidak tersedia' }}
                        &middot; urutan {{ $faq->urutan }}
                    </p>
                </div>
            @empty
                <p style="font-size:14px; color:var(--ink-soft); margin-top:8px;">Belum ada FAQ.</p>
            @endforelse
        </div>

    </div>

    {{-- Petugas yang belum menyentuh laporan apa pun dalam 30 hari. --}}
    @if ($petugasAktif === 0)
        <div class="dark-card" style="margin-top:24px;">
            <div style="position:relative; z-index:2;">
                <p style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.16em; color:var(--on-band);">
                    Perhatian
                </p>
                <h2 style="font-size:20px; font-weight:800; margin-top:8px;">
                    Tidak ada admin yang aktif menangani laporan.
                </h2>
                <p style="font-size:14px; color:var(--on-band); line-height:1.7; margin-top:10px;">
                    Tidak ada satu pun akun admin aktif yang mengubah status
                    laporan dalam 30 hari terakhir. Periksa daftar akun --
                    mungkin akunnya sudah dinonaktifkan tanpa disengaja.
                </p>
            </div>
        </div>
    @endif

</div>

@endsection