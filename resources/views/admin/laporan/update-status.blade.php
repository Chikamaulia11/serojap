@extends('layouts.admin')

@section('title', 'Update Status — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">

    <nav class="mb-4 text-sm" aria-label="Navigasi remah roti">
        <a href="{{ route('admin.dashboard') }}" class="text-[var(--accent)] hover:underline">&larr; Dashboard</a>
        <span class="text-gray-400 mx-1">/</span>
        <span class="text-gray-600">Update Status</span>
    </nav>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Update Status Laporan</h1>
        <p class="text-gray-500 text-sm mt-1">
            Pilih satu laporan, lalu catat perkembangannya. Setiap penyimpanan
            ditambahkan sebagai entri riwayat baru.
        </p>
    </div>

    {{-- STATISTIK SINGKAT --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        @foreach ($statistik as $kunci => $jumlah)
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3">
                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide truncate">
                    {{ \App\Support\StatusPeta::get($kunci)['label'] }}
                </div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ $jumlah }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- ============ KIRI: PILIH LAPORAN ============ --}}
        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50 space-y-3">
                <h2 class="text-sm font-semibold text-gray-700">
                    Daftar Laporan
                    <span class="ml-1 bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">{{ $daftarLaporan->total() }}</span>
                </h2>

                <form method="GET" action="{{ route('admin.laporan.update-status') }}" class="flex gap-2">
                    <label for="update-status-search" class="sr-only">Cari alamat atau keterangan</label>

                    <input type="search"
                           name="search"
                           id="update-status-search"
                           value="{{ request('search') }}"
                           placeholder="Cari alamat atau keterangan..."
                           maxlength="100"
                           class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-[var(--accent)] text-white hover:bg-[var(--accent-deep)] transition">
                        Cari
                    </button>
                </form>
            </div>

            <div class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto">
                @forelse ($daftarLaporan as $item)
                    @php
                        $terpilih = $laporan && $laporan->id === $item->id;
                        $statusTerbaru = $item->latestStatus;
                    @endphp

                    <a href="{{ route('admin.laporan.update-status', ['search' => request('search'), 'laporan' => $item->id]) }}"
                       @class([
                           'block px-5 py-4 transition',
                           'bg-blue-50 border-l-4 border-[var(--accent)]' => $terpilih,
                           'hover:bg-gray-50 border-l-4 border-transparent' => ! $terpilih,
                       ])
                       @if ($terpilih) aria-current="true" @endif>

                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-gray-900 text-sm truncate">
                                    {{ $item->nomor_referensi }}
                                </div>
                                <div class="text-xs text-gray-500 truncate mt-0.5">{{ $item->alamat }}</div>
                            </div>

                            <x-status-badge :status="$statusTerbaru?->status" class="flex-shrink-0" />
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 px-5">
                        <div class="text-3xl mb-2" aria-hidden="true">🔎</div>
                        <p class="text-sm font-semibold text-gray-800">Tidak ada laporan yang cocok</p>
                        <p class="text-xs text-gray-500 mt-1">Coba kata kunci lain atau kosongkan pencarian.</p>
                    </div>
                @endforelse
            </div>

            @if ($daftarLaporan->hasPages())
                <nav class="px-5 py-4 border-t border-gray-200" role="navigation" aria-label="Navigasi halaman laporan">
                    {{ $daftarLaporan->onEachSide(1)->links() }}
                </nav>
            @endif
        </section>

        {{-- ============ KANAN: FORM UNTUK LAPORAN TERPILIH ============ --}}
        <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden {{ $laporan ? '' : 'lg:sticky lg:top-6' }}">
            @if (! $laporan)
                {{-- Tanpa laporan terpilih, halaman ini tidak akan pernah
                     bisa dipakai. Dulu view ini memakai `$laporan` sebagai
                     Paginator padahal controller mengirimnya sebagai satu
                     laporan, sehingga `$laporan->total()` memanggil method
                     yang tidak ada dan halaman ini selalu error. --}}
                <div class="px-6 py-16 text-center">
                    <div class="text-4xl mb-3" aria-hidden="true">👈</div>
                    <h2 class="font-semibold text-gray-900">Pilih laporan terlebih dahulu</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Klik salah satu laporan di daftar sebelah kiri untuk membuka form update status.
                    </p>
                </div>
            @else
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-gray-700">{{ $laporan->nomor_referensi }}</h2>
                        <x-status-badge :status="$laporan->latestStatus?->status" />
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $laporan->alamat }}</p>
                </div>

                <form method="POST"
                      action="{{ route('admin.laporan.update', $laporan->id) }}"
                      enctype="multipart/form-data"
                      class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Status Baru
                        </label>

                        <select name="status" id="status" required
                                class="w-full bg-gray-50 border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition">
                            <option value="">-- Pilih status --</option>
                            @foreach (\App\Models\Report::STATUS as $kunci => $label)
                                <option value="{{ $kunci }}"
                                        @selected(old('status', $laporan->latestStatus?->status) === $kunci)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-gray-500 mt-1.5">
                            Memilih status yang sama tetap diperbolehkan untuk menambah catatan progres.
                        </p>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label for="keterangan" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Keterangan Petugas
                        </label>

                        <textarea name="keterangan" id="keterangan" rows="5" required
                                  minlength="5" maxlength="1000"
                                  placeholder="Jelaskan tindakan yang sudah dilakukan atau informasi terbaru untuk pelapor."
                                  class="w-full bg-gray-50 border @error('keterangan') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition">{{ old('keterangan') }}</textarea>

                        <div class="mt-1 flex items-center justify-between gap-2">
                            @error('keterangan')
                                <p class="text-red-600 text-[11px]">{{ $message }}</p>
                            @else
                                <p class="text-gray-400 text-[11px]">Minimal 5 karakter, maksimal 1000.</p>
                            @enderror

                            <p class="text-gray-400 text-[11px] shrink-0">
                                <span id="keterangan-count">{{ strlen(old('keterangan', '')) }}</span>/1000
                            </p>
                        </div>
                    </div>

                    {{-- Foto perbaikan --}}
                    <div x-data="{ nama: '', ukuran: 0, error: '' }">
                        <label for="foto_perbaikan" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Foto Perbaikan (Opsional)
                        </label>

                        <input type="file" name="foto_perbaikan" id="foto_perbaikan"
                               accept="image/jpeg,image/png"
                               x-ref="input"
                               x-on:change="const f = $event.target.files[0];
                                          if (!f) { nama = ''; ukuran = 0; return; }
                                          nama = f.name;
                                          ukuran = f.size;
                                          error = (f.size > 2097152) ? 'Ukuran foto melebihi 2 MB.'
                                               : (!['image/jpeg','image/png'].includes(f.type) ? 'Format harus JPG atau PNG.' : '');"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-semibold hover:file:bg-blue-100 cursor-pointer">

                        <p x-show="nama" x-cloak class="mt-2 text-xs text-gray-600">
                            <span x-text="nama"></span>
                            <span class="text-gray-400">
                                (<span x-text="(ukuran / 1024).toFixed(0)"></span> KB)
                            </span>
                        </p>

                        <p x-show="error" x-cloak x-text="error" class="mt-2 text-xs font-semibold text-red-600" role="alert"></p>

                        @error('foto_perbaikan')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <x-submit-button
                            type="submit"
                            loading-text="Menyimpan..."
                            class="flex-1 bg-[var(--accent)] hover:bg-[var(--accent-deep)] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]"
                        >
                            Simpan Pembaruan
                        </x-submit-button>

                        <a href="{{ route('admin.laporan.show', $laporan->id) }}"
                           class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition text-center">
                            Lihat Detail
                        </a>
                    </div>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Penghitung karakter untuk textarea keterangan.
    (function () {
        const textarea = document.getElementById('keterangan');
        const counter = document.getElementById('keterangan-count');

        if (!textarea || !counter) return;

        const perbarui = () => { counter.textContent = String(textarea.value.length); };

        textarea.addEventListener('input', perbarui);
        perbarui();
    })();
</script>
@endpush
