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
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
