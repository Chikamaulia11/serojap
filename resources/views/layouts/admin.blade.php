<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Serojap Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', 'Roboto', 'Poppins', 'Public Sans', sans-serif; }
        .font-serojap { font-family: 'Inter', sans-serif; font-weight: 700; }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/mdi/css/materialdesignicons.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-slate-800 antialiased">

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 w-60 h-screen bg-white border-r border-slate-200 flex flex-col z-50 overflow-y-auto">

        <!-- Brand -->
        <div class="flex items-center gap-2.5 px-5 py-5 border-b border-slate-100">
            <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}" alt="Serojap" class="w-10 h-10 rounded-lg object-cover shadow-md">
            <span class="text-lg font-bold text-[#2657c1] tracking-wide">SEROJAP</span>
        </div>

        <!-- Menu Utama -->
        <div class="py-5 px-4 pb-1.5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-2">Menu Utama</p>
        </div>

        <nav class="flex-1 px-2 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                <i class="mdi mdi-view-dashboard-outline text-lg w-5 text-center"></i>
                Dashboard
            </a>

            {{-- Manajemen Laporan (dropdown) --}}
            <div x-data="{ open: {{ request()->routeIs('admin.laporan.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                        {{ request()->routeIs('admin.laporan.*') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
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
                            {{ request()->routeIs('admin.laporan.index') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                        <i class="mdi mdi-file-document-outline text-lg w-5 text-center"></i>
                        Daftar Laporan
                    </a>

                    {{-- Update Status --}}
                    <a href="{{ route('admin.laporan.update-status') }}"
                        class="flex items-center gap-2.5 px-4 py-2 rounded-lg text-sm font-medium transition
                            {{ request()->routeIs('admin.laporan.update-status') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                        <i class="mdi mdi-clipboard-check-outline text-lg w-5 text-center"></i>
                        Update Status
                    </a>

                </div>
            </div>

            {{-- Manajemen FAQ --}}
            <a href="{{ route('admin.laporan.index') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.faq.*') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                <i class="mdi mdi-frequently-asked-questions text-lg w-5 text-center"></i>
                Manajemen FAQ
            </a>

            {{-- Grafik Statistik --}}
            <a href="{{ route('admin.laporan.index') }}"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium transition
                    {{ request()->routeIs('admin.statistik.*') ? 'bg-blue-50 text-[#2657c1]' : 'text-slate-500 hover:bg-slate-50 hover:text-[#2657c1]' }}">
                <i class="mdi mdi-chart-bar text-lg w-5 text-center"></i>
                Grafik Statistik
            </a>

        </nav>

        <!-- Footer / User Info -->
        <div class="mt-auto px-4 py-4 border-t border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-gradient-to-br from-[#2657c1] to-[#226d71] rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400">{{ ucfirst(auth()->user()->role ?? 'admin') }}</p>
                </div>
            </div>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center gap-2.5 px-4 py-2.5 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 mt-2 transition">
                <i class="mdi mdi-logout text-lg w-5 text-center"></i>
                Logout
            </a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}">@csrf</form>
        </div>

    </aside>

    <!-- Main Content -->
    <div class="ml-60 flex flex-col min-h-screen">
        <main class="flex-1 p-8">
            @yield('content')
        </main>
        <footer class="py-4 px-8 text-sm text-slate-400 text-center border-t border-slate-200 bg-white">
            &copy; {{ date('Y') }} Serojap — Sistem Pelaporan Jalan Rusak
        </footer>
    </div>

</body>
</html>