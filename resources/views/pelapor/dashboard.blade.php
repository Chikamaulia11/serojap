@extends('layouts.app')

@section('title', 'Dashboard Pelapor — Serojap')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard Pelapor</h1>
                <p class="text-lg text-gray-600 mb-8">Selamat datang, {{ auth()->user()->name }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-file-document-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Laporan</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-check-circle-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Selesai</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->whereHas('statuses', function($q){
                                        $q->where('status', 'selesai');
                                    })->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-8 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-4">
                                <i class="mdi mdi-clock-outline text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Diproses</p>
                                <p class="text-3xl font-bold">@php
                                    echo auth()->user()->laporan()->whereHas('statuses', function($q){
                                        $q->where('status', 'proses');
                                    })->count();
                                @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Laporan Terakhir</h2>
                        <a href="{{ route('laporan.create') }}" class="bg-[#2657c1] hover:bg-[#1e4ba8] text-white px-6 py-2.5 rounded-lg font-medium transition duration-200">
                            Buat Laporan Baru
                        </a>
                    </div>
                    @php $recent = auth()->user()->laporan()->latest()->take(5)->get(); @endphp
                    @if($recent->count() > 0)
                    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recent as $laporan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($laporan->lokasi, 40) }}</div>
                                            <div class="text-sm text-gray-500">{{ $laporan->kecamatan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $laporan->statusTerbaru?->status == 'selesai' ? 'bg-green-100 text-green-800' : ($laporan->statusTerbaru?->status == 'proses' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ $laporan->statusTerbaru?->status ?? 'baru' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $laporan->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="mdi mdi-file-document-outline text-6xl text-gray-400 mb-4 block"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada laporan</h3>
                        <p class="text-gray-500 mb-6">Mulai laporkan jalan rusak di sekitar Anda</p>
                        <a href="{{ route('laporan.create') }}" class="bg-[#2657c1] hover:bg-[#1e4ba8] text-white px-8 py-3 rounded-xl font-semibold text-lg transition duration-200 inline-block">
                            Buat Laporan Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
