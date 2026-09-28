@extends('layouts.app')

@section('title', 'Buat Laporan')
@section('deskripsi', 'Kirim laporan kerusakan jalan di Kabupaten Purwakarta beserta foto dan titik lokasi yang tepat.')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endpush

@section('content')

<div class="container">

    <a href="{{ route('dashboard') }}" class="back-btn">
        <span aria-hidden="true">&larr;</span> Kembali ke Dashboard
    </a>

    <div class="card">

        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo SEROJAP">
            <div>
                <h2>Laporan Kerusakan Jalan</h2>
                <p class="header-sub">Isi bagian yang kamu bisa. Petugas akan menindaklanjuti.</p>
            </div>
        </div>

        {{-- Ringkasan error, sekaligus target fokus keyboard. --}}
        @if ($errors->any())
            <div class="error-box" role="alert" tabindex="-1" id="ringkasan-error">
                <p class="error-box-judul">Ada {{ $errors->count() }} yang perlu diperbaiki:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('laporan.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="form-laporan"
            novalidate
        >
            @csrf

            <div class="grid">

                {{-- ============ FOTO ============ --}}
                <div class="field full" x-data="{ namaFile: '', ukuran: 0, error: '' }">
                    <label for="foto" class="field-label">
                        Foto kerusakan <span class="wajib" aria-hidden="true">*</span>
                    </label>

                    <p class="field-help" id="foto-help">
                        Format JPG atau PNG, maksimal 5 MB. Foto dari iPhone yang berformat
                        HEIC perlu diubah dulu: Settings &rsaquo; Camera &rsaquo; Formats &rsaquo; Most Compatible.
                    </p>

                    <input
                        type="file"
                        name="foto"
                        id="foto"
                        class="field-input file-input @error('foto') field-invalid @enderror"
                        accept="image/jpeg,image/png"
                        required
                        aria-describedby="foto-help foto-error"
                        @error('foto') aria-invalid="true" @enderror
                        x-on:change="
                            namaFile = $event.target.files[0]?.name ?? '';
                            ukuran = $event.target.files[0]?.size ?? 0;
                            error = '';
                            if (namaFile && ukuran > 5242880) { error = 'Ukuran foto ' + (ukuran/1048576).toFixed(1) + ' MB, maksimal 5 MB.'; }
                        "
                    >

                    <p class="field-error" id="foto-error" x-show="error" x-text="error" x-cloak></p>
                    @error('foto')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                    <div class="foto-preview" x-show="namaFile && !error" x-cloak>
                        {{-- `src` sengaja TIDAK ditulis. `<img src="">` membuat
                             browser meminta URL halaman saat ini sebagai gambar,
                             yang selalu gagal -> request sia-sia plus ikon gambar
                             rusak. `src` baru diisi `form.js` setelah pengguna
                             memilih file. --}}
                        <img id="foto-preview-img" alt="Pratinjau foto yang akan diunggah">
                        <span id="foto-preview-nama" class="foto-preview-nama"></span>
                    </div>
                </div>

                {{-- ============ LOKASI / PETA ============ --}}
                <div class="field full" id="lokasi-area">
                    <label for="alamat" class="field-label">
                        Alamat lokasi <span class="wajib" aria-hidden="true">*</span>
                    </label>

                    <p class="field-help" id="alamat-help">
                        Ketik nama jalan, desa, atau kecamatan. Pilih dari daftar yang muncul,
                        atau tekan tombol di bawah untuk menandai titik tepat di peta.
                    </p>

                    <div class="autocomplete-wrapper">
                        <input
                            type="text"
                            id="alamat"
                            name="alamat"
                            class="field-input"
                            placeholder="Contoh: Jl. Raya-Wanayasa, Kertajaya"
                            autocomplete="off"
                            role="combobox"
                            aria-expanded="false"
                            aria-controls="autocomplete-list"
                            aria-autocomplete="list"
                            aria-describedby="alamat-help alamat-error"
                            value="{{ old('alamat') }}"
                            maxlength="255"
                            required
                            @error('alamat') aria-invalid="true" @enderror
                        >

                        <ul id="autocomplete-list" class="autocomplete-list" role="listbox" aria-label="Saran lokasi"></ul>
                    </div>

                    @error('alamat')
                        <p class="field-error" id="alamat-error">{{ $message }}</p>
                    @enderror

                    <div class="lokasi-opsi">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="tombol-peta"
                            aria-expanded="false"
                            aria-controls="mapContainer"
                        >
                            <span aria-hidden="true">&var(--selesai);</span> Tandai di Peta
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="tombol-gps"
                        >
                            <span aria-hidden="true">&var(--selesai);</span> Pakai Lokasi Saya
                        </button>
                    </div>

                    <p class="field-help" id="gps-catatan" x-data="{ tampil: false }" x-show="tampil" x-cloak>
                        Browser sedang meminta izin lokasi. Kalau muncul pesan di HP, pilih
                        <strong>Izinkan</strong>. Pilih juga <strong>Akan minta setiap kali</strong>
                        supaya peta tidak langsung tertutup.
                    </p>

                    <div id="mapContainer" class="full" hidden>
                        <div id="map" role="application" aria-label="Peta interaktif. Ketuk lokasi kerusakan pada peta."></div>
                        <p class="map-hint">Ketuk lokasi kerusakan pada peta untuk menandai titiknya.</p>
                    </div>

                    <div class="koordinat">
                        <div>
                            <span class="koordinat-label">Lintang (latitude)</span>
                            <output id="lat" name="latitude" class="koordinat-nilai">-</output>
                        </div>
                        <div>
                            <span class="koordinat-label">Bujur (longitude)</span>
                            <output id="lng" name="longitude" class="koordinat-nilai">-</output>
                        </div>
                    </div>

                    {{-- ============ KOORDINAT MANUAL ============
                         Ini BUKAN field tersembunyi. Sebelumnya di sini
                         ada dua `type="hidden"`, padahal teks bantuan
                        WUOTO di halaman menyuruh pengguna "isi koordinat
                         secara manual kalau peta gagal dimuat" -- mustahil,
                         karena tidak ada satu pun field yang bisa diketik.

                         Akibatnya siapa pun yang peta/GPS-nya gagal tidak
                         punya jalan keluar sama sekali: laporan terkirim
                         `null` ke server dan selalu ditolak.

                         Sekarang kedua input-nya number asli di dalam
                         `<details>` yang terbuka sendiri saat peta gagal
                         (lihat `form.js`). `form.js` tetap menulis ke id
                         yang sama, jadi penandaan di peta langsung mengisi
                         kedua field ini.
                         ============================ --}}
                    <details id="koordinat-manual" class="koordinat-manual">
                        <summary class="koordinat-manual-summary">
                            <span aria-hidden="true">&#9906;</span>
                            Isi koordinat manual
                        </summary>

                        <p class="field-help">
                            Buka bagian ini hanya kalau peta tidak bisa dipakai. Salin angka
                            lintang dan bujur dari aplikasi peta HP Anda.
                        </p>

                        <div class="koordinat-manual-grid">
                            <div>
                                <label class="koordinat-label" for="input-latitude">Lintang (latitude)</label>
                                <input
                                    type="number"
                                    name="latitude"
                                    id="input-latitude"
                                    value="{{ old('latitude') }}"
                                    step="any"
                                    min="-90"
                                    max="90"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    class="koordinat-input"
                                    @error('latitude') aria-invalid="true" aria-describedby="latitude-error" @enderror
                                >
                            </div>

                            <div>
                                <label class="koordinat-label" for="input-longitude">Bujur (longitude)</label>
                                <input
                                    type="number"
                                    name="longitude"
                                    id="input-longitude"
                                    value="{{ old('longitude') }}"
                                    step="any"
                                    min="-180"
                                    max="180"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    class="koordinat-input"
                                    @error('longitude') aria-invalid="true" aria-describedby="longitude-error" @enderror
                                >
                            </div>
                        </div>
                    </details>

                    @error('latitude')
                        <p class="field-error" id="latitude-error">{{ $message }}</p>
                    @enderror
                    @error('longitude')
                        <p class="field-error" id="longitude-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ============ KETERANGAN ============ --}}
                {{--
                    Dua jebakan di sini, keduanya sudah pernah muncul.

                    1) `x-data` HARUS di wrapper ini, bukan di <textarea>.

                    Sebelumnya scope-nya narrowed ke <textarea> itu saja,
                    sedangkan <p id="keterangan-hitung"> adalah saudaranya
                    di luar scope. Alpine tidak bisa menjangkau variabel
                    `hitung` dari luar, jadi x-text selalu kosong dan
                    penghitung karakter tidak pernah tampil.

                    2) `this` di dalam `x-on` BUKAN elemen.

                    Di Alpine, `this` menunjuk ke scope data, bukan ke
                    elemen yang menerima event. `this.value` jadi
                    `undefined`, lalu `.length` di atasnya melempar
                    TypeError setiap kali ada yang mengetik, dan
                    penghitungnya tidak pernah bergerak. Yang benar
                    adalah magic `$el`.
                --}}
                <div class="field full" x-data="{ hitung: {{ strlen(old('keterangan') ?? '') }} }">
                    <label for="keterangan" class="field-label">
                        Keterangan <span class="wajib" aria-hidden="true">*</span>
                    </label>

                    <p class="field-help" id="keterangan-help">
                        Jelaskan kerusakan: lubang, retak, genangan, atau jalan ambles. Sebutkan
                        sejak kapan dan seberapa berbahaya menurutmu. Minimal 10 karakter.
                    </p>

                    <textarea
                        name="keterangan"
                        id="keterangan"
                        class="field-input"
                        rows="5"
                        maxlength="1000"
                        placeholder="Contoh: Ada lubang besar diameter sekitar 50 cm di sisi kanan jalan, sudah ada sejak dua minggu lalu dan semakin lebar saat hujan."
                        aria-describedby="keterangan-help keterangan-error keterangan-hitung"
                        required
                        @error('keterangan') aria-invalid="true" @enderror
                        x-on:input="hitung = $el.value.length"
                    >{{ old('keterangan') }}</textarea>

                    <p class="field-kabut" id="keterangan-hitung" x-text="hitung + ' / 1000 karakter'"></p>

                    @error('keterangan')
                        <p class="field-error" id="keterangan-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ============ NAMA ============ --}}
                <div class="field full">
                    <label for="nama" class="field-label">Nama pelapor</label>

                    <p class="field-help" id="nama-help">
                        Nama ini hanya dibaca petugas. Sudah diisi otomatis dari nama
                        akun kamu, ganti kalau mau nama yang berbeda.
                    </p>

                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        class="field-input"
                        placeholder="{{ auth()->user()->name }}"
                        value="{{ old('nama', auth()->user()->name) }}"
                        maxlength="255"
                        required
                        aria-describedby="nama-help"
                    >

                    @error('nama')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="full">
                    <x-submit-button
                        type="submit"
                        loading-text="Mengunggah..."
                        class="btn btn-primary w-full"
                    >
                        Kirim Laporan
                    </x-submit-button>

                    <p class="field-help text-center">
                        Setelah dikirim, laporan langsung masuk antrean petugas dan bisa kamu
                        pantau di <a href="{{ route('laporan.my-report') }}">Riwayat Saya</a>.
                    </p>
                </div>

            </div>
        </form>

    </div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="{{ asset('js/form.js') }}"></script>

@endsection
