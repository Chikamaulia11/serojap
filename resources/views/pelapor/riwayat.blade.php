@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">

<h2 class="title">Riwayat Laporan</h2>

<div class="list">

@foreach($reports as $r)
    <div class="card-report"
        onclick="openPopup(
            '{{ $r->alamat }}',
            '{{ $r->status }}',
            `{{ $r->keterangan }}`
        )">

        <p><b>{{ $r->alamat }}</b></p>
        <p class="status">{{ $r->status }}</p>

        <p style="font-size:12px; color:gray;">
            {{ $r->created_at->format('d M Y, H:i') }}
        </p>
    </div>
@endforeach

</div>

<div id="popup" class="popup">
    <div class="popup-content">
        <h3>Detail Laporan</h3>

        <p><b>Alamat:</b></p>
        <p id="pop-alamat"></p>

        <p><b>Status:</b></p>
        <p id="pop-status"></p>

        <p><b>Keterangan:</b></p>
        <p id="pop-keterangan"></p>

        <button class="btn-close" onclick="closePopup()">Selesai</button>
    </div>
</div>

<script src="{{ asset('js/riwayat.js') }}"></script>

@endsection