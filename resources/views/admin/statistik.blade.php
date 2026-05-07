@extends('layouts.admin')

@section('title', 'Statistik - Serojap')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-bold" style="color: #2657c1;">Statistik Laporan</h1>
        <p class="text-xl mt-2 text-gray-600">Analisis data laporan jalan rusak Purwakarta</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl p-8 shadow-lg">
            <h3 class="text-2xl font-bold mb-6">Distribusi Kecamatan</h3>
            <p class="text-gray-500">Coming soon: Chart per kecamatan</p>
        </div>
        <div class="bg-white rounded-2xl p-8 shadow-lg">
            <h3 class="text-2xl font-bold mb-6">Status Laporan</h3>
            <p class="text-gray-500">Coming soon: Pie chart status</p>
        </div>
    </div>
</div>
@endsection
