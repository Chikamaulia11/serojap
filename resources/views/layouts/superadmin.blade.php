<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    @include('partials.favicon')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme-bootstrap')

    <title>@yield('title', 'Serojap Super Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/mdi/css/materialdesignicons.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', 'Roboto', 'Poppins', 'Public Sans', sans-serif;
            /* Warna kilau ikut aksen aktif. Semula `color-mix(in srgb, var(--accent) 8%, transparent)`
               -- itu biru admin lama, jadi tetap biru meski pengguna memilih
               aksen lain. `color-mix()` dengan alfa rendah memberi
               tingkat opasitas yang sama tanpa mengunci warnanya. */
            background:
                radial-gradient(
                    circle at top right,
                    color-mix(in srgb, var(--accent) 8%, transparent),
                    transparent 34%
                ),
                var(--accent-tint);
        }

        /* Panel sidebar superadmin SELALU putih di kedua mode.
         *
         * `rgba(255,255,255,.94)` adalah kaca buram di atas foto/gradien
         * terang. Kalau dipetakan ke `var(--surface)`, mode dark
         * menjadikannya gelap dan panel hilang terhadap foto di
         * belakangnya. Jadi latar dikunci apa adanya.
         *
         * Konsekuensinya: token tinta di dalam sidebar juga harus dikunci
         * ke nilai LIGHT. Kalau dibiarkan mengikuti mode, di mode dark
         * `--ink-soft` jadi #a7b6b3 (terang) di atas panel putih -- 1.88:1,
         * dan teks menu praktis tak terbaca. Aksen pun sama: `--accent`
         * mode dark_only1.98:1 s.d. 2.76:1 di keenam aksen.
         *
         * Override-nya lokal ke sidebar, jadi konten area utama tetap
         * mengikuti mode seperti seharusnya.
         *
         * Rasio di panel #f1f2f2 (putis .94 di atas gradien terang):
         *   --ink        #1c2624  13.85:1
         *   --ink-soft   #5b6a68   5.05:1
         *   --ink-mute   #586765   5.28:1
         *   --accent-ink  8.08:1 (amber, terendah) s.d. 11.09:1
         */
        .superadmin-sidebar {
            width: 260px;
            background: rgba(255,255,255,0.94);
            backdrop-filter: blur(18px);
            border-right: 1px solid var(--line);
            box-shadow: 8px 0 24px rgba(15, 23, 42, 0.04);

            /* Kunci token ke nilai light -- lihat catatan di atas. */
            --ink: #1c2624;
            --ink-soft: #5b6a68;
            --ink-mute: #586765;
            --surface: #ffffff;
            --surface-2: #f7f9f9;
            --bg: #f1f5f5;
            --line: #e2e8e8;
        }

        .superadmin-main {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .superadmin-content {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px;
        }

        .sidebar-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 700;
            transition: 0.25s ease;
            text-decoration: none;
        }

        .sidebar-link.active {
            background: var(--accent-tint);
            color: var(--accent-ink);
            box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 8%, transparent);
        }

        .sidebar-link:not(.active) {
            color: var(--ink-soft);
        }

        .sidebar-link:not(.active):hover {
            background: var(--bg);
            color: var(--accent-ink);
        }

        .sidebar-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            color: var(--ink-mute);
            flex-shrink: 0;
        }

        .sidebar-link.active .sidebar-icon {
            background: var(--surface);
            color: var(--accent-ink);
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 13px 16px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 800;
            color: var(--danger);
            background: var(--surface);
            border: 1px solid var(--danger-tint);
            text-decoration: none;
            transition: 0.25s ease;
            box-shadow: 0 10px 24px rgba(239, 68, 68, 0.05);
        }

        .logout-link:hover {
            background: var(--danger-tint);
            transform: translateY(-2px);
        }

        .page-animate {
            animation: fadeUp 0.35s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .swal2-popup {
            border-radius: 22px !important;
        }

        /*
         * Sidebar super admin sebelumnya hanya menyusut jadi 235px
         * di bawah 1024px dan tidak pernah disembunyikan. Di HP
         * selebar ~360px, 235px menutup hampir dua pertiga layar
         * tanpa ada tombol untuk menutupnya.
         *
         * Sekarang jadi drawer: di bawah `md` sidebar meluncur
         * keluar layar dan konten memakai lebar penuh.
         */
        @media (max-width: 767px) {
            .superadmin-sidebar {
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                box-shadow: 8px 0 24px rgba(15, 23, 42, 0.12);
            }

            /* ditulis inline oleh Alpine saat drawer dibuka */
            .superadmin-sidebar.terbuka {
                transform: translateX(0);
            }

            .superadmin-main {
                margin-left: 0;
                width: 100%;
            }

            .superadmin-content {
                padding: 16px;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .superadmin-sidebar {
                width: 235px;
            }

            .superadmin-main {
                margin-left: 235px;
                width: calc(100% - 235px);
            }

            .superadmin-content {
                padding: 22px;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="text-slate-800 antialiased"
      x-data="{ sidebarTerbuka: false }"
      x-on:keydown.escape.window="sidebarTerbuka = false">

    <!-- Top bar khusus layar kecil -->
    <div class="md:hidden sticky top-0 z-40 flex items-center gap-3 h-14 px-4 bg-white border-b border-slate-200">
        <button
            type="button"
            class="inline-flex items-center justify-center w-10 h-10 -ml-1 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
            x-on:click="sidebarTerbuka = ! sidebarTerbuka"
            x-bind:aria-expanded="sidebarTerbuka ? 'true' : 'false'"
            aria-controls="superadminSidebar"
            aria-label="Buka menu navigasi"
        >
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <span class="font-bold text-blue-700 tracking-wide">SEROJAP</span>
    </div>

    <!-- Lapisan gelap di belakang drawer -->
    <div
        class="md:hidden fixed inset-0 z-40 bg-slate-900/40"
        x-show="sidebarTerbuka"
        x-cloak
        x-on:click="sidebarTerbuka = false"
        aria-hidden="true"
    ></div>

    <!-- Sidebar -->
    <aside
        id="superadminSidebar"
        class="superadmin-sidebar fixed top-0 left-0 h-screen z-50 flex flex-col"
        x-bind:class="sidebarTerbuka ? 'terbuka' : ''"
    >

        <!-- Brand -->
        <div class="px-5 py-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <x-logo :size="44" />

                <div>
                    <div class="text-xl font-extrabold text-[var(--accent-ink)] leading-tight">
                        SEROJAP
                    </div>

                    <div class="text-xs text-slate-500 font-semibold mt-0.5">
                        Super Admin Panel
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu -->
        <div class="px-5 pt-6 pb-2">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.18em]">
                Menu Super Admin
            </p>
        </div>

        <nav class="flex-1 min-h-0 overflow-y-auto px-4 space-y-2">

            <a href="{{ route('superadmin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon">
                    <i class="mdi mdi-view-dashboard-outline text-xl"></i>
                </span>
                Dashboard
            </a>

            <a href="{{ route('superadmin.accounts.index') }}"
               class="sidebar-link {{ request()->routeIs('superadmin.accounts.*') ? 'active' : '' }}">
                <span class="sidebar-icon">
                    <i class="mdi mdi-account-multiple-outline text-xl"></i>
                </span>
                Manajemen Akun
            </a>

        </nav>

        <!-- Logout -->
        <div class="mt-auto px-4 py-4 border-t border-slate-200">

            {{-- Picker tema, di atas Logout. Di layar kecil sidebar ini
                 yang jadi menu (drawer), jadi satu penempatan ini
                 melayani desktop dan mobile sekaligus. --}}
            @include('partials.theme-picker', ['variant' => 'sidebar'])

            <a href="#"
               id="superAdminLogoutButton"
               class="logout-link">
                <i class="mdi mdi-logout text-xl"></i>
                Logout
            </a>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>

        </div>

    </aside>

    <!-- Main Content -->
    <div class="superadmin-main">

        <main class="flex-1">
            <div class="superadmin-content page-animate">
                @yield('content')
            </div>
        </main>

        <footer class="py-4 px-6 text-sm text-slate-500 text-center border-t border-slate-200 bg-white/70 backdrop-blur">
            &copy; {{ date('Y') }} SEROJAP — Sistem Pelaporan Jalan Rusak Purwakarta | Super Admin
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutButton = document.getElementById('superAdminLogoutButton');
            const logoutForm = document.getElementById('logout-form');

            if (logoutButton && logoutForm) {
                logoutButton.addEventListener('click', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Logout dari Super Admin?',
                        text: 'Kamu akan keluar dari halaman super admin.',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Logout',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: 'var(--danger)',
                        cancelButtonColor: 'var(--ink-soft)',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            logoutForm.submit();
                        }
                    });
                });
            }
        });
    </script>

    @stack('scripts')

</body>
</html>