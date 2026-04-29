<!DOCTYPE html>
<html>
<head>
    <title>SEROJAP</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
</head>

<body>

<!-- ERROR -->
@if ($errors->any())
<div class="error-box">
    @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<!-- POPUP -->
@if(session('success'))
<div class="popup">
    <div class="popup-content">
        <div class="checkmark">✔</div>
        <p>{{ session('success') }}</p>

        <div class="popup-buttons">
            <button class="btn-popup btn-selesai" onclick="goToRiwayat()">Selesai</button>
            <button class="btn-popup btn-kembali" onclick="resetForm()">Kembali</button>
        </div>

    </div>
</div>
@endif

<div class="container">
    <div class="card">

        <div class="header">
            <img src="{{ asset('logo.png') }}">
            <h2>SEROJAP</h2>
        </div>

        <form action="/report" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid">

                <input type="text" name="nama" placeholder="Nama Pelapor">

                <input type="file" name="foto">

                <input type="text" id="alamat" name="alamat" class="full" placeholder="Titik Kerusakan">

                <button type="button" class="btn btn-alt full" onclick="toggleMap()">
                    📍 Tentukan Lokasi di Peta
                </button>

                <div id="mapContainer" class="full">
                    <div id="map"></div>
                </div>

                <input type="text" id="lat" name="latitude" readonly placeholder="Latitude">
                <input type="text" id="lng" name="longitude" readonly placeholder="Longitude">

                <textarea name="keterangan" class="full" placeholder="Keterangan"></textarea>

                <button class="btn btn-main full">Kirim Laporan</button>

            </div>

        </form>

    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('js/form.js') }}"></script>

</body>
</html>