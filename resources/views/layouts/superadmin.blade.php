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
            min-height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', 'Roboto', 'Poppins', 'Public Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(38, 87, 193, 0.08), transparent 35%),
                linear-gradient(180deg, #f8fafc 0%, #eef3f9 100%);
        }

        .font-serojap {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
        }

        .superadmin-sidebar {
            width: 16rem;
        }

        .superadmin-main {
            margin-left: 16rem;
            min-height: 100vh;
            width: calc(100% - 16rem);
        }

        .superadmin-content {
            padding: 2rem;
        }

        @media (max-width: 1024px) {
            .superadmin-sidebar {
                width: 15rem;
            }

            .superadmin-main {
                margin-left: 15rem;
                width: calc(100% - 15rem);
            }

            .superadmin-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .superadmin-sidebar {
                position: relative !important;
                width: 100%;
                height: auto;
            }

            .superadmin-main {
                margin-left: 0;
                width: 100%;
            }

            .superadmin-content {
                padding: 1rem;
            }
        }

        .superadmin-scrollbar::-webkit-scrollbar {
            width: 7px;
        }

        .superadmin-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .superadmin-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .swal2-popup {
            border-radius: 22px !important;
            font-family: 'Inter', sans-serif !important;
        }
    </style>
</head>

<body class="text-slate-800 antialiased">

    <!-- Sidebar Super Admin -->
    <aside class="superadmin-sidebar fixed top-0 left-0 h-screen bg-white/95 backdrop-blur-xl border-r border-slate-200 flex flex-col z-50 overflow-y-auto superadmin-scrollbar shadow-[8px_0_30px_rgba(15,23,42,0.04)]">

        <!-- Brand -->
        <div class="px-5 py-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}"
                         alt="Serojap"
                         class="w-9 h-9 object-contain">
                </div>

                <div>
                    <span class="text-xl font-extrabold text-[#2657c1] tracking-wide block leading-tight">
                        SEROJAP
                    </span>
                    <span class="text-xs text-slate-400 font-semibold tracking-wide">
                        Super Admin Panel
                    </span>
                </div>
            </div>
        </div>

        <!-- Menu Utama -->
        <div class="pt-6 px-5 pb-2">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.22em]">
                Menu Super Admin
            </p>
        </div>

        <nav class="flex-1 px-3 space-y-1">

            <a href="{{ route('superadmin.dashboard') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition
                {{ request()->routeIs('superadmin.dashboard') ? 'bg-blue-50 text-[#2657c1] shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                <span class="w-9 h-9 rounded-xl flex items-center justify-center transition
                    {{ request()->routeIs('superadmin.dashboard') ? 'bg-white text-[#2657c1]' : 'bg-transparent text-slate-400 group-hover:bg-white group-hover:text-[#2657c1]' }}">
                    <i class="mdi mdi-view-dashboard-outline text-xl"></i>
                </span>
                Dashboard
            </a>

            <a href="{{ route('superadmin.accounts.index') }}"
               class="group flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition
                {{ request()->routeIs('superadmin.accounts.*') ? 'bg-blue-50 text-[#2657c1] shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                <span class="w-9 h-9 rounded-xl flex items-center justify-center transition
                    {{ request()->routeIs('superadmin.accounts.*') ? 'bg-white text-[#2657c1]' : 'bg-transparent text-slate-400 group-hover:bg-white group-hover:text-[#2657c1]' }}">
                    <i class="mdi mdi-account-multiple-outline text-xl"></i>
                </span>
                Manajemen Akun
            </a>

            <div class="mt-5 mx-2 rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-cyan-50 p-4">
                <div class="w-10 h-10 rounded-xl bg-white text-[#2657c1] flex items-center justify-center shadow-sm mb-3">
                    <i class="mdi mdi-shield-check-outline text-xl"></i>
                </div>

                <p class="text-xs font-bold text-slate-800">
                    Akses Tertinggi
                </p>

                <p class="text-[11px] text-slate-500 leading-relaxed mt-1">
                    Super admin mengelola akun admin dan pelapor dari area terpisah.
                </p>
            </div>

        </nav>

        <!-- Footer / User Info -->
        <div class="mt-auto px-4 py-4 border-t border-slate-100">

            <div class="flex items-center gap-3 rounded-2xl p-3 bg-slate-50 border border-slate-100">

                <div class="w-10 h-10 bg-gradient-to-br from-[#2657c1] to-[#226d71] rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden flex-shrink-0">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             alt="Profil"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">
                        {{ auth()->user()->name ?? 'Super Admin' }}
                    </p>

                    <p class="text-xs text-slate-400">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'super_admin')) }}
                    </p>
                </div>
            </div>

            <a href="#"
               id="superAdminLogoutButton"
               class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl text-sm font-bold text-red-500 hover:bg-red-50 mt-3 transition">
                <i class="mdi mdi-logout text-lg"></i>
                Logout
            </a>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>

        </div>

    </aside>

    <!-- Main Content -->
    <div class="superadmin-main flex flex-col">
        <main class="superadmin-content flex-1">
            @yield('content')
        </main>

        <footer class="py-5 px-8 text-sm text-slate-400 text-center border-t border-slate-200 bg-white/80 backdrop-blur">
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