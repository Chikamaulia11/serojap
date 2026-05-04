<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Laporan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
</head>

<body>

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
    </div>
@endforeach

</div>

<!-- POPUP -->
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

</body>
</html>
