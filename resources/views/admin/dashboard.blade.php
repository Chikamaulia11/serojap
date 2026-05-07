@extends('layouts.admin')

@section('title', 'Dashboard Admin — Serojap')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-12">
        <div class="flex items-center gap-6 bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
            <img src="{{ asset('assets/pelapor/images/logo-serojap.jpeg') }}" alt="Serojap" class="w-24 h-24 rounded-xl shadow-lg object-cover border-4 border-[#2657c1]">
            <div>
                <h1 class="text-4xl font-bold" style="color: #2657c1;">Dashboard Admin</h1>
                <p class="text-xl mt-2" style="color: #226d71;">Selamat datang, {{ auth()->user()->name }}</p>
                <p class="text-gray-600 mt-1">Kelola laporan pelaporan dengan efisien</p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $total = \App\Models\TabelLaporan::count();
        $baru  = \App\Models\TabelLaporan::doesntHave('statuses')->count();
        $proses = \App\Models\TabelStatus::where('status','proses')->distinct('id_laporan')->count('id_laporan');
        $selesai = \App\Models\TabelStatus::where('status','selesai')->distinct('id_laporan')->count('id_laporan');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Laporan</p>
                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">{{ $total }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#eff6ff] text-[#2657c1]">Semua Status</span>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Laporan Baru</p>
                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">{{ $baru }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600">Menunggu</span>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Diproses</p>
                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">{{ $proses }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600">Aktif</span>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">{{ $selesai }}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">Sukses</span>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.laporan.index') }}" class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-all">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold" style="color: #2657c1;">Manajemen Laporan</h3>
                    <p class="text-lg text-gray-600 mt-2">Kelola dan verifikasi semua laporan masuk</p>
                    <p class="text-sm text-gray-500 mt-1">Update status & filter laporan</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

