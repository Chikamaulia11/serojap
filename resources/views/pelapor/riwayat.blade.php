@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">

<h2 class="title">Riwayat Laporan</h2>

<div class="list">

@forelse($reports as $r)

    @php
        $status = $r->statusTerbaru->status ?? 'diterima';

        $warnaStatus = [
            'diterima' => '#10b981',
            'diproses' => '#f59e0b',
            'selesai'  => '#8b5cf6',
            'ditolak'  => '#ef4444',
        ][$status] ?? '#6b7280';

        $keterangan = $r->statusTerbaru->keterangan ?? 'Belum ada keterangan';

        $koordinat = ($r->latitude && $r->longitude)
            ? $r->latitude . ', ' . $r->longitude
            : '-';

        $tanggal = $r->created_at
            ? $r->created_at->format('d M Y, H:i')
            : '-';
    @endphp

    <div
        class="card-report"
        onclick="document.getElementById('popup-{{ $r->id }}').style.display='flex'"
    >
        <p><b>{{ $r->alamat }}</b></p>

        <p class="status" style="color: {{ $warnaStatus }};">
            {{ ucfirst($status) }}
        </p>

        <p style="font-size:12px; color:gray;">
            {{ $tanggal }}
        </p>
    </div>

    <div id="popup-{{ $r->id }}" class="popup">
        <div class="popup-content">

            <h3>Detail Laporan</h3>

            <p><b>Alamat:</b></p>
            <p>{{ $r->alamat ?: '-' }}</p>

            <br>

            <p><b>Status:</b></p>
            <p>{{ ucfirst($status) }}</p>

            <br>

            <p><b>Keterangan:</b></p>
            <p>{{ $keterangan }}</p>

            <br>

            <p><b>Koordinat:</b></p>
            <p>{{ $koordinat }}</p>

            <br>

            <p><b>Tanggal:</b></p>
            <p>{{ $tanggal }}</p>

            <button
                type="button"
                class="btn-close"
                onclick="event.stopPropagation(); document.getElementById('popup-{{ $r->id }}').style.display='none'"
            >
                Selesai
            </button>

        </div>
    </div>

@empty

    <div class="card-report">
        <p>Belum ada laporan</p>
    </div>

@endforelse

</div>

@endsection