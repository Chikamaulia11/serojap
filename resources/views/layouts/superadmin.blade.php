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

        @media (max-width: 1024px) {
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
</head>

<body class="text-slate-800 antialiased">

    <!-- Sidebar -->
    <aside class="superadmin-sidebar fixed top-0 left-0 h-screen z-50 flex flex-col overflow-y-auto">

        <!-- Brand -->
        <div class="px-5 py-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white shadow-md shadow-blue-100 border border-slate-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}"
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

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link">
                <span class="sidebar-icon">
                    <i class="mdi mdi-shield-account-outline text-xl"></i>
                </span>
                Area Admin
            </a>

            <div class="mt-5 rounded-3xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-[#2657c1] mb-3">
                    <i class="mdi mdi-shield-check-outline text-2xl"></i>
                </div>

                <h3 class="text-sm font-extrabold text-slate-800">
                    Akses Tertinggi
                </h3>

                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Mengelola akun admin dan pelapor dari area terpisah.
                </p>
            </div>

        </nav>

        <!-- User Info -->
        <div class="mt-auto px-4 py-4 border-t border-slate-200">

            <div class="flex items-center gap-3 rounded-2xl p-3 bg-white shadow-sm border border-slate-100">

                <div class="w-10 h-10 bg-gradient-to-br from-[#2657c1] to-[#226d71] rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden flex-shrink-0">
                    @if(auth()->user()?->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             alt="Profil"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    @endif
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-extrabold text-slate-800 truncate">
                        {{ auth()->user()->name ?? 'Super Admin' }}
                    </p>

                    <p class="text-xs text-slate-400 font-semibold">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'super_admin')) }}
                    </p>
                </div>
            </div>

            <a href="#"
               id="superAdminLogoutButton"
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold text-red-500 hover:bg-red-50 mt-3 transition">
                <i class="mdi mdi-logout text-xl w-5 text-center"></i>
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

</body>
</html>