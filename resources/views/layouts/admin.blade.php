<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme-bootstrap')
    <title>@yield('title', 'Serojap Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', 'Roboto', 'Poppins', 'Public Sans', sans-serif; }
        .font-serojap { font-family: 'Inter', sans-serif; font-weight: 700; }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/mdi/css/materialdesignicons.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-white font-sans text-slate-800 antialiased"
      x-data="{ sidebarTerbuka: false }"
      x-on:keydown.escape.window="sidebarTerbuka = false">

    <!-- Sidebar -->
    {{--
        Sidebar admin sebelumnya `fixed w-60` tanpa handling mobile
        sama sekali: di HP selebar ~360px, sidebar 240px menutup
        hampir dua pertiga layar, tidak punya apa pun untuk
        menutupnya, dan konten utama tetap terdorong `ml-60`.

        Sekarang jadi drawer: tersembunyi di layar kecil, menjadi
        sidebar tetap mulai breakpoint `md`, dan bisa dibuka lewat
        tombol hamburger yang punya `aria-expanded` serta
        `aria-controls` yang menunjuk ke `id` sidebar.
    --}}
    <!-- Top bar khusus layar kecil -->
    <div class="md:hidden sticky top-0 z-40 flex items-center gap-3 h-14 px-4 bg-white border-b border-slate-200">
        <button
            type="button"
            class="inline-flex items-center justify-center w-10 h-10 -ml-1 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]"
            x-on:click="sidebarTerbuka = ! sidebarTerbuka"
            x-bind:aria-expanded="sidebarTerbuka ? 'true' : 'false'"
            aria-controls="adminSidebar"
            aria-label="Buka menu navigasi"
        >
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <span class="font-bold text-[var(--accent)] tracking-wide">SEROJAP</span>
    </div>

    <!-- Lapisan gelap di belakang drawer -->
    <div
        class="md:hidden fixed inset-0 z-40 bg-slate-900/40"
        x-show="sidebarTerbuka"
        x-cloak
        x-on:click="sidebarTerbuka = false"
        aria-hidden="true"
    ></div>

    {{--
        Transform ditulis lewat `x-bind:style`, bukan `x-bind:class`.

        Alasannya `x-bind:class` baru berlaku setelah Alpine start,
        jadi sebelum itu `<aside>` sama sekali tidak punya
        `-translate-x-full` dan sidebar ikut melompat ke layar
        seketika di HP lalu baru menghilang. Dengan inline style,
        kondisi tertutup sama sekali tidak menghasilkan style --
        jadi aturan statis `-translate-x-full md:translate-x-0`
        yang berlaku, dan hanya saat terbuka transform-nya ditulis
        inline. Tidak ada bentrok antara `-translate-x-full` dan
        `md:translate-x-0` yang urutan CSS-nya tidak terjamin.
    --}}
    <aside
        id="adminSidebar"
        class="fixed top-0 left-0 z-50 w-60 h-screen bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 -translate-x-full md:translate-x-0"
        x-bind:style="sidebarTerbuka ? 'transform: translateX(0)' : ''"
    >

        <!-- Brand -->
        <div class="flex items-center gap-2.5 px-5 py-5 border-b border-slate-100">
            <img src="{{ asset('assets/pelapor/images/logo-serojap.webp') }}" alt="Serojap" class="w-10 h-10 rounded-lg object-cover shadow-md">
            <span class="text-lg font-bold text-[var(--accent)] tracking-wide">SEROJAP</span>
        </div>

        <!-- Menu Utama -->
        <div class="py-5 px-4 pb-1.5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Menu Utama</p>
        </div>

        <nav class="flex-1 min-h-0 overflow-y-auto px-2 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                <i class="mdi mdi-view-dashboard-outline text-lg w-5 text-center"></i>
                Dashboard
            </a>

            {{-- Manajemen Laporan (dropdown) --}}
            <div x-data="{ open: {{ request()->routeIs('admin.laporan.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                        {{ request()->routeIs('admin.laporan.*') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                    <span class="flex items-center gap-2.5">
                        <i class="mdi mdi-clipboard-text-outline text-lg w-5 text-center"></i>
                        Manajemen Laporan
                    </span>
                    <i class="mdi mdi-chevron-down text-lg transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-transition class="mt-1 ml-4 space-y-0.5">

                    {{-- Semua Laporan --}}
                    <a href="{{ route('admin.laporan.index') }}"
                        class="flex items-center gap-2.5 px-4 py-2 rounded-lg text-sm font-medium transition
                            {{ request()->routeIs('admin.laporan.index') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                        <i class="mdi mdi-file-document-outline text-lg w-5 text-center"></i>
                        Daftar Laporan
                    </a>

                    {{-- Update Status --}}
                    <a href="{{ route('admin.laporan.update-status') }}"
                        class="flex items-center gap-2.5 px-4 py-2 rounded-lg text-sm font-medium transition
                            {{ request()->routeIs('admin.laporan.update-status') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                        <i class="mdi mdi-clipboard-check-outline text-lg w-5 text-center"></i>
                        Update Status
                    </a>

                </div>
            </div>

            {{-- Manajemen FAQ --}}
            <a href="{{ route('admin.manajemen-faq.index') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.manajemen-faq.*') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                <i class="mdi mdi-frequently-asked-questions text-lg w-5 text-center"></i>
                Manajemen FAQ
            </a>

            {{-- Grafik Statistik --}}
            <a href="{{ route('admin.statistik.index') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.statistik.*') ? 'bg-blue-50 text-[var(--accent)]' : 'text-slate-500 hover:bg-slate-50 hover:text-[var(--accent)]' }}">
                <i class="mdi mdi-chart-bar text-lg w-5 text-center"></i>
                Grafik Statistik
            </a>

            {{--
                "Manajemen Admin" DIHAPUS dari sidebar ini.

                Dua alasan, keduanya independen:

                1. Route `admin.admin-accounts.*` tidak pernah ada --
                   contasnya (`AdminAccountController`) tidak
                   terdaftar di routes/web.php dan view-nya juga tidak
                   pernah dibuat. Kalau blok ini masih di sini dan
                   pernah dievaluasi, `route()` langsung exception.

                2. Blok ini tidak akan pernah dievaluasi. Letaknya di
                   `layouts/admin.blade.php`, sedangkan
                   `AdminMiddleware` selalu mengembalikan super admin
                   ke `superadmin.dashboard` -- jadi tidak ada super
                   admin yang pernah melihat layout ini.

                Manajemen akun yang berfungsi ada di
                `superadmin.accounts.*` dan sudah ditautkan dari
                dashboard super admin.
            --}}

        </nav>

        <!-- Footer / User Info -->
        <div class="mt-auto px-4 py-4 border-t border-slate-100">

            {{-- Picker tema, dekat profil dan Keluar. Di layar kecil
                 sidebar ini yang jadi menu (drawer), jadi satu
                 penempatan ini melayani desktop dan mobile sekaligus. --}}
            @include('partials.theme-picker', ['variant' => 'sidebar'])

            <a href="{{ route('admin.profile.index') }}"
                class="flex items-center gap-2.5 rounded-lg p-2 hover:bg-slate-50 transition">

                <div class="w-9 h-9 bg-gradient-to-br from-[var(--accent)] to-[var(--accent-deep)] rounded-full flex items-center justify-center text-white font-bold text-sm overflow-hidden">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                             alt="Profil"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}</p>
                </div>
            </a>

            <a href="#"
                id="adminLogoutButton"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 mt-2 transition">
                <i class="mdi mdi-logout text-lg w-5 text-center"></i>
                Logout
            </a>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>
        </div>

    </aside>

    <!-- Main Content -->
    <div class="md:ml-60 flex flex-col min-h-screen">
        <main class="flex-1 p-4 sm:p-6 md:p-8">
            @yield('content')
        </main>
        <footer class="py-4 px-4 sm:px-6 md:px-8 text-sm text-slate-400 text-center border-t border-slate-200 bg-white">
            &copy; {{ date('Y') }} Serojap — Sistem Pelaporan Jalan Rusak
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutButton = document.getElementById('adminLogoutButton');
            const logoutForm = document.getElementById('logout-form');

            if (logoutButton && logoutForm) {
                logoutButton.addEventListener('click', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Logout?',
                        text: 'Kamu akan keluar dari halaman admin.',
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