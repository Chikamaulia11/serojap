@extends('layouts.admin')

@section('title', 'Detail Laporan #' . $laporan->id_laporan . ' — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Laporan
    </a>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Laporan</h1>
            <p class="text-gray-500 text-sm mt-1">ID #{{ $laporan->id_laporan }} · Dikirim {{ \Carbon\Carbon::parse($laporan->created_at)->diffForHumans() }}</p>
        </div>
@php $statusNow = $laporan->statusTerbaru?->status ?? 'baru'; @endphp
        @php
            $badgeClasses = [
                'baru' => 'bg-gray-100 text-gray-700',
                'diterima' => 'bg-emerald-100 text-emerald-700',
                'proses' => 'bg-amber-100 text-amber-700',
                'selesai' => 'bg-purple-100 text-purple-700',
                'ditolak' => 'bg-red-100 text-red-700',
            ][$statusNow] ?? 'bg-gray-100 text-gray-700';
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}">
            {{ ucfirst($statusNow) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Pelapor --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Data Pelapor
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 text-white flex items-center justify-center font-bold text-lg">
                            {{ strtoupper(substr($laporan->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $laporan->user->name ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $laporan->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Laporan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Detail Laporan
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @if($laporan->foto)
                        <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto Kerusakan" class="w-full h-64 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-full h-48 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-sm">Tidak ada foto</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kecamatan</p>
                            <p class="text-gray-900">{{ $laporan->kecamatan }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Lokasi / Koordinat</p>
                            <p class="text-gray-900">{{ $laporan->lokasi }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Kerusakan</p>
                        <p class="text-gray-900 leading-relaxed">{{ $laporan->deskripsi }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Laporan</p>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

    </div>
</div>
@endsection

