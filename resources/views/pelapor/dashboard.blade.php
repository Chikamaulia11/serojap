@extends('layouts.app')

@section('title', 'Dashboard Pelapor - Serojap')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="mb-12">
        <div class="bg-gradient-to-r from-[#2657c1] to-[#1e4ba8] text-white p-8 rounded-2xl shadow-2xl">
            <h1 class="text-4xl font-bold mb-4">Dashboard Pelapor</h1>
            <p class="text-xl">Halo, {{ auth()->user()->name }}! Pantau laporan aduan Anda di sini.</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m0 0h3m-6 0h6"/>
                    </svg>
                </div>
                <div class="ml-6">
                    <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-6">
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['selesai'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan Table --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Laporan Saya</h2>
                <a href="{{ route('pelapor.laporan.create') }}" class="bg-gradient-to-r from-[#2657c1] to-[#1e4ba8] text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                    + Laporan Baru
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($laporans as $laporan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">#{{ $laporan->id_laporan }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $laporan->kecamatan }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($laporan->lokasi, 40) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($laporan->statusTerakhir)
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $laporan->statusTerakhir->status == 'selesai' ? 'bg-emerald-100 text-emerald-800' : 
                                       ($laporan->statusTerakhir->status == 'proses' ? 'bg-blue-100 text-blue-800' : 
                                       ($laporan->statusTerakhir->status == 'diterima' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($laporan->statusTerakhir->status) }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Baru</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $laporan->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('pelapor.laporan.show', $laporan->id_laporan) }}" class="text-[#2657c1] hover:text-[#1e4ba8] font-semibold">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p>Belum ada laporan. <a href="{{ route('pelapor.laporan.create') }}" class="text-[#2657c1] hover:underline font-semibold">Buat laporan pertama Anda</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $laporans->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
