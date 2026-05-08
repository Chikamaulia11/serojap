@extends('layouts.app')

@section('title', 'Detail Laporan - Serojap')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#2657c1] to-[#1e4ba8] px-8 py-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Laporan #{{ $laporan->id_laporan }}</h1>
                    <p class="text-xl mt-2 opacity-90">{{ $laporan->kecamatan }}, {{ $laporan->lokasi }}</p>
                </div>
                <span class="px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm text-sm font-bold">
                    @if($laporan->statusTerakhir)
                        {{ ucfirst($laporan->statusTerakhir->status) }}
                    @else
                        Menunggu Verifikasi
                    @endif
                </span>
            </div>
        </div>

        <div class="p-8 space-y-8">
            {{-- Detail Laporan --}}
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Deskripsi Masalah</h3>
                    <p class="text-lg text-gray-700 leading-relaxed">{{ $laporan->deskripsi }}</p>
                    <p class="text-sm text-gray-500 mt-4">Dilaporkan pada {{ $laporan->created_at->format('d M Y H:i') }}</p>
                </div>

                @if($laporan->foto)
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Foto Bukti</h3>
                    <img src="{{ Storage::url($laporan->foto) }}" alt="Foto laporan" class="w-full h-64 object-cover rounded-2xl shadow-lg">
                </div>
                @endif
            </div>

            {{-- Riwayat Status --}}
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-6">Riwayat Status</h3>
                @if($laporan->statuses->count() > 0)
                    <div class="space-y-4">
                        @foreach($laporan->statuses->sortByDesc('created_at') as $status)
                        <div class="flex items-start space-x-4 p-6 bg-gray-50 rounded-2xl">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-bold bg-white text-gray-900 shadow-sm">
                                        {{ ucfirst($status->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500">{{ $status->created_at->format('d M Y H:i') }}</p>
                                </div>
                                @if($status->keterangan)
                                    <p class="mt-2 text-gray-700">{{ $status->keterangan }}</p>
                                @endif
                                @if($status->user)
                                    <p class="mt-1 text-xs text-gray-500">Oleh: {{ $status->user->name }}</p>
                                @endif
                                @if($status->foto_perbaikan)
                                    <div class="mt-3">
                                        <img src="{{ Storage::url($status->foto_perbaikan) }}" alt="Perbaikan" class="w-32 h-32 object-cover rounded-xl shadow-md">
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-lg text-gray-500">Laporan masih dalam antrian verifikasi admin.</p>
                    </div>
                @endif
            </div>

            <div class="pt-8 border-t border-gray-200">
                <a href="{{ route('pelapor.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition-all shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
