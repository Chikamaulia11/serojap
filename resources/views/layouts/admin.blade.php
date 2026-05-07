<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Admin — SEROJAP')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <div class="min-h-screen flex">

            {{-- SIDEBAR ADMIN --}}
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed h-full z-30">
                <div class="h-16 flex items-center px-6 border-b border-gray-200">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-xl font-bold">
                        <img src="{{ asset('assets/pelapor/images/logo-serojap.jpeg') }}" alt="Serojap" class="w-10 h-10 rounded-lg object-cover shadow-md mr-3">
                        <span style="color: #2657c1; font-weight: 900; letter-spacing: -0.025em;">SEROJAP</span>
                    </a>
                </div>

                @php
                    $showSubMenuLaporan = false;
                    $showStatistik      = false;
                    $showFaq            = false;
                    $showAkunAdmin      = false;
                @endphp

                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto" x-data="{ openLaporan: {{ request()->routeIs('admin.laporan.*') ? 'true' : 'false' }} }">
                    {{-- Manajemen Laporan Group --}}
                    <div>
                        @if($showSubMenuLaporan)
                            <button @click="openLaporan = !openLaporan"
                                    class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                                    {{ request()->routeIs('admin.laporan.*') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Manajemen Laporan
                                </div>
                                <svg :class="openLaporan ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="openLaporan" x-collapse class="mt-1 ml-4 pl-4 border-l border-gray-200 space-y-1">
                                <a href="{{ route('admin.laporan.index') }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition
                                   {{ request()->routeIs('admin.laporan.index') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.laporan.index') ? 'bg-[#2657c1]' : 'bg-gray-300' }}"></span>
                                    Daftar Laporan
                                </a>
                                <a href="{{ route('admin.laporan.update-status') }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition
                                   {{ request()->routeIs('admin.laporan.update-status') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.laporan.update-status') ? 'bg-[#2657c1]' : 'bg-gray-300' }}"></span>
                                    Update Status
                                </a>
                                <a href="{{ route('admin.laporan.riwayat-status') }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition
                                   {{ request()->routeIs('admin.laporan.riwayat-status') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.laporan.riwayat-status') ? 'bg-[#2657c1]' : 'bg-gray-300' }}"></span>
                                    Riwayat Status
                                </a>
                            </div>
                        @else
                            <a href="{{ route('admin.laporan.index') }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                               {{ request()->routeIs('admin.laporan.*') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Manajemen Laporan
                            </a>
                        @endif
                    </div>

                    @if($showStatistik)
                    <a href="{{ route('admin.statistik.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                       {{ request()->routeIs('admin.statistik.*') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Statistik
                    </a>
                    @endif

                    @if($showFaq)
                    <a href="{{ route('admin.faq.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                       {{ request()->routeIs('admin.faq.*') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Manajemen FAQ
                    </a>
                    @endif

                    @if($showAkunAdmin)
                    <a href="{{ route('admin.akun.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                       {{ request()->routeIs('admin.akun.*') ? 'bg-[#eff6ff] text-[#2657c1]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Akun Admin
                    </a>
                    @endif
                </nav>

                <div class="p-4 border-t border-gray-200">
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div class="w-8 h-8 rounded-full bg-[#eff6ff] text-[#2657c1] flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-[#1f4674] hover:bg-[#eff6ff] rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 ml-64">
                @if(session('success'))
                    <div class="fixed top-4 right-4 z-50 bg-[#eff6ff] border border-[#2657c1] text-[#2657c1] px-4 py-3 rounded-lg shadow-sm flex items-center gap-2 animate-fade-in-down">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="fixed top-4 right-4 z-50 bg-[#eff6ff] border border-[#2657c1] text-[#2657c1] px-4 py-3 rounded-lg shadow-sm flex items-center gap-2 animate-fade-in-down">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="p-8">
                    @yield('content')
                </div>
            </main>
        </div>

        <style>
            @keyframes fadeInDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-down {
                animation: fadeInDown 0.3s ease-out;
            }
        </style>
    </body>
</html>

