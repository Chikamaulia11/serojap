@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">

<h2 class="title">
    Riwayat Laporan
</h2>

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

        $keterangan = $r->statusTerbaru->keterangan
            ?? 'Belum ada keterangan';

    @endphp

    <div
        class="card-report"
        onclick="openPopup(
            '{{ $r->alamat }}',
            '{{ ucfirst($status) }}',
            `{{ $keterangan }}`,
            '{{ $r->latitude }}',
            '{{ $r->longitude }}',
            '{{ $r->created_at->format('d M Y, H:i') }}'
        )"
    >

        <p>
            <b>{{ $r->alamat }}</b>
        </p>

        <p
            class="status"
            style="color: {{ $warnaStatus }};"
        >
            {{ ucfirst($status) }}
        </p>

        <p
            style="font-size:12px; color:gray;"
        >
            {{ $r->created_at->format('d M Y, H:i') }}
        </p>

    </div>

@empty

    <div class="card-report">

        <p>
            Belum ada laporan
        </p>

    </div>

@endforelse

</div>

<div id="popup" class="popup">

    <div class="popup-content">

        <h3>
            Detail Laporan
        </h3>

        <p>
            <b>Alamat:</b>
        </p>

        <p id="pop-alamat"></p>

        <br>

        <p>
            <b>Status:</b>
        </p>

        <p id="pop-status"></p>

        <br>

        <p>
            <b>Keterangan:</b>
        </p>

        <p id="pop-keterangan"></p>

        <br>

        <p>
            <b>Koordinat:</b>
        </p>

        <p id="pop-koordinat"></p>

        <br>

        <p>
            <b>Tanggal:</b>
        </p>

        <p id="pop-tanggal"></p>

        <button
            class="btn-close"
            onclick="closePopup()"
        >
            Selesai
        </button>

    </div>

</div>

<script src="{{ asset('js/riwayat.js') }}"></script>

@endsection