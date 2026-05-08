@extends('layouts.app')

@section('title', 'Buat Laporan Baru - Serojap')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Laporkan Jalan Rusak</h1>
        <p class="text-xl text-gray-600">Isi detail kerusakan jalan untuk segera ditangani</p>
    </div>

    <div class="bg-white shadow-2xl rounded-3xl p-8 md:p-12">
        <form method="POST" action="{{ route('pelapor.laporan.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div>
                <label for="lokasi" class="block text-sm font-bold text-gray-700 mb-3">Lokasi Tepat *</label>
                <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" required
                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#2657c1] focus:border-transparent transition duration-300 text-lg @error('lokasi') border-red-300 bg-red-50 @enderror"
                    placeholder="Contoh: Jalan Raya Bogor KM 12, depan SPBU">
                @error('lokasi')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kecamatan" class="block text-sm font-bold text-gray-700 mb-3">Kecamatan *</label>
                <select id="kecamatan" name="kecamatan" required class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#2657c1] focus:border-transparent transition duration-300 text-lg @error('kecamatan') border-red-300 bg-red-50 @enderror">
                    <option value="">Pilih Kecamatan</option>
                    <option value="Kiarapedes" {{ old('kecamatan') == 'Kiarapedes' ? 'selected' : '' }}>Kiarapedes</option>
                    <option value="Purwakarta" {{ old('kecamatan') == 'Purwakarta' ? 'selected' : '' }}>Purwakarta</option>
                    <option value="Campaka" {{ old('kecamatan') == 'Campaka' ? 'selected' : '' }}>Campaka</option>
                    <option value="Jatiluhur" {{ old('kecamatan') == 'Jatiluhur' ? 'selected' : '' }}>Jatiluhur</option>
                    <option value="Miri Tani" {{ old('kecamatan') == 'Miri Tani' ? 'selected' : '' }}>Miri Tani</option>
                    <option value="Sirkam" {{ old('kecamatan') == 'Sirkam' ? 'selected' : '' }}>Sirkam</option>
                    <option value="Vastukota" {{ old('kecamatan') == 'Vastukota' ? 'selected' : '' }}>Vastukota</option>
                </select>
                @error('kecamatan')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-bold text-gray-700 mb-3">Deskripsi Kerusakan *</label>
                <textarea id="deskripsi" name="deskripsi" rows="6" required
                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#2657c1] focus:border-transparent transition duration-300 text-lg @error('deskripsi') border-red-300 bg-red-50 @enderror"
                    placeholder="Jelaskan kondisi jalan rusak, seperti: lubang besar, retak parah, longsor, bahaya untuk pengendara...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="foto" class="block text-sm font-bold text-gray-700 mb-3">Foto Bukti (Opsional)</label>
                <input type="file" id="foto" name="foto" accept="image/*"
                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-[#2657c1] focus:border-transparent transition duration-300 text-lg file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#2657c1] file:text-white hover:file:bg-[#1e4ba8]">
                <p class="mt-2 text-sm text-gray-500">Upload foto kerusakan jalan (max 2MB, JPG/PNG)</p>
                @error('foto')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-[#2657c1] to-[#1e4ba8] text-white px-8 py-6 rounded-2xl font-bold text-xl hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 shadow-lg">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Laporan
                </button>
                <a href="{{ route('pelapor.dashboard') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-8 py-6 rounded-2xl font-bold text-xl text-center transition-all duration-300 hover:shadow-lg">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
