@extends('layouts.admin')

@section('title', 'Dashboard Admin — Serojap')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-12">
        <div class="flex items-center gap-6 bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
            <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}" alt="Serojap" class="w-24 h-24 rounded-xl shadow-lg object-cover">

            <div>
                <h1 class="text-4xl font-bold" style="color: #2657c1;">
                    Dashboard Admin
                </h1>

                <p class="text-xl mt-2" style="color: #226d71;">
                    Selamat datang, {{ auth()->user()->name }}
                </p>

                <p class="text-gray-600 mt-1">
                    Kelola laporan pelaporan dengan efisien
                </p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php

        $total = \App\Models\Report::count();

        $diterima = \App\Models\TabelStatus::where('status', 'diterima')
            ->distinct('report_id')
            ->count('report_id');

        $diproses = \App\Models\TabelStatus::where('status', 'diproses')
            ->distinct('report_id')
            ->count('report_id');

        $selesai = \App\Models\TabelStatus::where('status', 'selesai')
            ->distinct('report_id')
            ->count('report_id');

    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

        {{-- TOTAL --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Laporan
                    </p>

                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">
                        {{ $total }}
                    </p>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#eff6ff] text-[#2657c1]">
                        Semua Status
                    </span>
                </div>

                <div class="w-16 h-16 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">

                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>

                    </svg>

                </div>

            </div>

        </div>

        {{-- DITERIMA --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Diterima
                    </p>

                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">
                        {{ $diterima }}
                    </p>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">
                        Masuk
                    </span>
                </div>

                <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">

                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"/>

                    </svg>

                </div>

            </div>

        </div>

        {{-- DIPROSES --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Diproses
                    </p>

                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">
                        {{ $diproses }}
                    </p>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                        Aktif
                    </span>
                </div>

                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">

                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0"/>

                    </svg>

                </div>

            </div>

        </div>

        {{-- SELESAI --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Selesai
                    </p>

                    <p class="text-4xl font-bold text-gray-900 mb-2 group-hover:text-[#2657c1] transition">
                        {{ $selesai }}
                    </p>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600">
                        Sukses
                    </span>
                </div>

                <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">

                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4"/>

                    </svg>

                </div>

            </div>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Manajemen Laporan --}}
        <a href="{{ route('admin.laporan.index') }}"
           class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-all flex-shrink-0">

                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12"/>

                    </svg>

                </div>

                <div>

                    <h3 class="text-xl font-bold" style="color: #2657c1;">
                        Manajemen Laporan
                    </h3>

                    <p class="text-lg text-gray-600 mt-2">
                        Kelola dan verifikasi semua laporan masuk
                    </p>

                </div>

            </div>

        </a>

        {{-- Manajemen FAQ --}}
        <a href="{{ route('admin.manajemen-faq.index') }}"
           class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg hover:shadow-2xl hover:border-[#2657c1] transition-all duration-300 group">

            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-all flex-shrink-0">

                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2"/>

                    </svg>

                </div>

                <div>

                    <h3 class="text-xl font-bold" style="color: #2657c1;">
                        Manajemen FAQ
                    </h3>

                    <p class="text-lg text-gray-600 mt-2">
                        Kelola dan update data FAQ
                    </p>

                </div>

            </div>

        </a>

        {{-- Grafik Statistik --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg">

            <div class="flex items-center gap-6">

                <div class="w-20 h-20 bg-gradient-to-br from-[#2657c1] to-[#1e4ba8] rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0">

                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6"/>

                    </svg>

                </div>

                <div>

                    <h3 class="text-xl font-bold" style="color: #2657c1;">
                        Grafik Statistik
                    </h3>

                    <p class="text-lg text-gray-600 mt-2">
                        Melihat visualisasi data pelaporan
                    </p>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection