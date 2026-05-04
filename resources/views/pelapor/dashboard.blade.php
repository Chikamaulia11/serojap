@extends('layouts.app')

@section('content')

<div class="hero">
    <h1>Selamat Datang, {{ $user->name }}</h1>
    <p>Sistem Pelaporan Jalan Rusak Purwakarta</p>
</div>

<div class="cards">

    <div class="card">
        <h3>Total Laporan</h3>
        <p>{{ $total }}</p>
    </div>

    <div class="card">
        <h3>Diterima</h3>
        <p>{{ $diterima }}</p>
    </div>

    <div class="card">
        <h3>Diproses</h3>
        <p>{{ $diproses }}</p>
    </div>

    <div class="card">
        <h3>Selesai</h3>
        <p>{{ $selesai }}</p>
    </div>

</div>

@endsection