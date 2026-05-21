@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/form.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- ERROR -->
@if ($errors->any())
<div class="error-box">
    @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<div class="container">

    <!-- BACK -->
    <a href="{{ route('dashboard') }}" class="back-btn">
        ← Kembali ke Dashboard
    </a>

    <div class="card">

        <div class="header">
            <img src="{{ asset('logo.png') }}">
            <h2>SEROJAP</h2>
        </div>

        <form 
            action="{{ route('laporan.store') }}" 
            method="POST" 
            enctype="multipart/form-data"
        >
            @csrf

            <div class="grid">

                <!-- NAMA -->
                <input 
                    type="text" 
                    name="nama" 
                    placeholder="Nama Pelapor"
                    value="{{ old('nama') }}"
                >

                <!-- FOTO -->
                <input 
                    type="file" 
                    name="foto"
                >

                <!-- ALAMAT -->
                <div class="autocomplete-wrapper full">

                    <input 
                        type="text" 
                        id="alamat" 
                        name="alamat" 
                        class="full"
                        placeholder="Cari lokasi kerusakan..."
                        autocomplete="off"
                        value="{{ old('alamat') }}"
                    >

                    <div id="autocomplete-list" class="autocomplete-list"></div>

                </div>

                <!-- BUTTON MAP -->
                <button 
                    type="button" 
                    class="btn btn-alt full" 
                    onclick="toggleMap()"
                >
                    📍 Tentukan Lokasi di Peta
                </button>

                <!-- MAP -->
                <div id="mapContainer" class="full">
                    <div id="map"></div>
                </div>

                <!-- LAT -->
                <input 
                    type="text" 
                    id="lat" 
                    name="latitude" 
                    readonly 
                    placeholder="Latitude"
                    value="{{ old('latitude') }}"
                >

                <!-- LNG -->
                <input 
                    type="text" 
                    id="lng" 
                    name="longitude" 
                    readonly 
                    placeholder="Longitude"
                    value="{{ old('longitude') }}"
                >

                <!-- KETERANGAN -->
                <textarea 
                    name="keterangan" 
                    class="full" 
                    placeholder="Keterangan"
                >{{ old('keterangan') }}</textarea>

                <!-- BUTTON -->
                <button class="btn btn-main full">
                    Kirim Laporan
                </button>

            </div>

        </form>

    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Laporan Berhasil Dikirim!',
    text: '{{ session('success') }}',
    confirmButtonColor: '#226d71',
    confirmButtonText: 'Lihat Riwayat'
}).then((result) => {

    if(result.isConfirmed){
        window.location.href = "/my-report";
    }

});
</script>
@endif

<script src="{{ asset('js/form.js') }}"></script>

@endsection