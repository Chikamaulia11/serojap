<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg">Selamat datang, <span class="p-6 text-gray-900">{{ auth()->user()->name }}</span>!</p>
                    <p class="text-gray-500 mt-1">Anda login sebagai <span class="font-medium">{{ ucfirst(auth()->user()->role) }}</span>.</p>
                </div>
            </div>

            {{-- Admin Quick Access (hanya untuk admin/super_admin) --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Panel Manajemen Admin</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    {{-- Laporan --}}
                    <a href="{{ route('admin.laporan.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Manajemen Laporan</p>
                                <p class="text-sm text-gray-500">Kelola laporan masuk</p>
                            </div>
                        </div>
                    </a>

                     <!-- Statistik (disembunyikan — hapus tanda komentar untuk menampilkan kembali)
                    <a href="{{ route('admin.statistik.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Statistik</p>
                                <p class="text-sm text-gray-500">Lihat ringkasan data</p>
                            </div>
                        </div>
                    </a> -->
                    

                     <!-- FAQ (disembunyikan — hapus tanda komentar untuk menampilkan kembali)
                    <a href="{{ route('admin.faq.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Manajemen FAQ</p>
                                <p class="text-sm text-gray-500">Kelola pertanyaan umum</p>
                            </div>
                        </div>
                    </a> -->
                    

                    <!-- Akun Admin (disembunyikan — hapus tanda komentar untuk menampilkan kembali)
                    <a href="{{ route('admin.akun.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-300 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Akun Admin</p>
                                <p class="text-sm text-gray-500">Kelola akun admin</p>
                            </div>
                        </div>
                    </a>
                     -->

                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
