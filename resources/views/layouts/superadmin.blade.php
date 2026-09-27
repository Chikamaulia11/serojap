<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            background:
                radial-gradient(circle at top right, rgba(38, 87, 193, 0.08), transparent 34%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .superadmin-sidebar {
            width: 260px;
            background: rgba(255,255,255,0.94);
            backdrop-filter: blur(18px);
            border-right: 1px solid #e2e8f0;
            box-shadow: 8px 0 24px rgba(15, 23, 42, 0.04);
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
            background: #eaf2ff;
            color: #2657c1;
            box-shadow: 0 10px 24px rgba(38, 87, 193, 0.08);
        }

        .sidebar-link:not(.active) {
            color: #64748b;
        }

        .sidebar-link:not(.active):hover {
            background: #f1f5f9;
            color: #2657c1;
        }

        .sidebar-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .sidebar-link.active .sidebar-icon {
            background: #ffffff;
            color: #2657c1;
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
            color: #ef4444;
            background: #fff;
            border: 1px solid #fee2e2;
            text-decoration: none;
            transition: 0.25s ease;
            box-shadow: 0 10px 24px rgba(239, 68, 68, 0.05);
        }

        .logout-link:hover {
            background: #fef2f2;
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
        class="superadmin-sidebar fixed top-0 left-0 h-screen z-50 flex flex-col overflow-y-auto"
        x-bind:class="sidebarTerbuka ? 'terbuka' : ''"
    >

        <!-- Brand -->
        <div class="px-5 py-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white shadow-md shadow-blue-100 border border-slate-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/pelapor/images/logo-serojap.webp') }}"
                         alt="Serojap"
                         class="w-8 h-8 object-contain">
                </div>

                <div>
                    <div class="text-xl font-extrabold text-[#2657c1] leading-tight">
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

        <nav class="flex-1 px-4 space-y-2">

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
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
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