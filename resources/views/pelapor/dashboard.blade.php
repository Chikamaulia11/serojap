@extends('layouts.app')

@section('content')

<div class="hero">
    <h1>Halo, {{ $user->name }}</h1>
    <p>Selamat datang di Sistem Pelaporan Jalan Rusak Purwakarta</p>
</div>

<div class="cards">

    <div class="card">
        <h3>Buat Laporan</h3>
        <p>Laporkan jalan rusak dengan lokasi akurat</p>
        <a href="/report" class="btn">Buat Laporan</a>
    </div>

    <div class="card">
        <h3>Riwayat Laporan</h3>
        <p>Lihat perkembangan laporan kamu</p>
        <a href="/my-report" class="btn">Lihat</a>
    </div>

    <div class="card">
        <h3>FAQ</h3>
        <p>Pertanyaan umum seputar sistem</p>
        <a href="/faq" class="btn">Buka FAQ</a>
    </div>

    <div class="card">
        <h3>Prosedur</h3>
        <p>Alur pelaporan dari awal hingga selesai</p>
        <a href="/prosedur" class="btn">Lihat</a>
    </div>

</div>

@endsection