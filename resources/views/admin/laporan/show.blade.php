@extends('layouts.admin')

@section('title', 'Detail Laporan #' . $laporan->id . ' — SEROJAP')

@section('content')

<div class="max-w-7xl mx-auto">

    @if(session('success'))

        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>

    @endif

    @if(session('error'))

        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>

    @endif

    <a
        href="{{ route('admin.laporan.index') }}"
        class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4 transition"
    >

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
            />
        </svg>

        Kembali ke Daftar Laporan

    </a>

    @php

        $statusNow = $laporan->statusTerbaru?->status ?? 'diterima';

        $badgeClasses = [

            'diterima' => 'bg-emerald-100 text-emerald-700',
            'diproses' => 'bg-amber-100 text-amber-700',
            'selesai'  => 'bg-purple-100 text-purple-700',
            'ditolak'  => 'bg-red-100 text-red-700',

        ][$statusNow] ?? 'bg-gray-100 text-gray-700';

    @endphp

    <div class="flex items-start justify-between mb-6 gap-6">

        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900">
                Detail Laporan
            </h1>

            <p class="text-gray-500 text-sm mt-1">
                ID #{{ $laporan->id }}
                ·
                Dikirim
                {{ $laporan->created_at ? $laporan->created_at->diffForHumans() : '' }}
            </p>
        </div>

        <div class="flex flex-col items-end gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}">
                {{ ucfirst($statusNow) }}
            </span>

            @if(isset($daftarLaporan) && $daftarLaporan->count())
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-3 w-auto min-w-[12rem]">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Ganti Laporan
                    </label>

                    <form method="GET" action="{{ route('admin.laporan.show', ['id' => $laporan->id]) }}">
                        <select
                            name="id"
                            onchange="window.location.href=this.value;"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2"
                        >
                            @foreach($daftarLaporan as $l)
                                <option
                                    value="{{ route('admin.laporan.show', ['id' => $l->id]) }}"
                                    {{ (int)$l->id === (int)$laporan->id ? 'selected' : '' }}
                                >
                                    #{{ $l->id }} — {{ $l->alamat }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================= KOLOM KIRI ================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Pelapor --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

                <div class="px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        Data Pelapor
                    </h2>

                </div>

                <div class="px-6 py-4">

                    <div class="flex items-center gap-4">

                        @php
                            $fotoProfilUrl = !empty($laporan->user?->foto_profil)
                                ? asset('storage/' . $laporan->user->foto_profil)
                                : null;
                        @endphp

                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 text-white flex items-center justify-center font-bold text-lg overflow-hidden">
                            @if($fotoProfilUrl)
                                <img
                                    src="{{ $fotoProfilUrl }}"
                                    alt="Foto Profil Pelapor"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                {{ strtoupper(substr($laporan->user->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>

                        <div>

                            <p class="font-semibold text-gray-900">
                                {{ $laporan->user->name ?? $laporan->nama_pelapor }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $laporan->user->email ?? '-' }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            {{-- Detail Laporan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

                <div class="px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Detail Laporan
                    </h2>

                </div>

                <div class="px-6 py-4 space-y-4">

                    @if($laporan->foto)

                        @php
                            $fotoKerusakanUrl = asset('storage/' . $laporan->foto);
                        @endphp

                        <button
                            type="button"
                            class="w-full"
                            onclick="showImageModal('{{ $fotoKerusakanUrl }}', 'Foto Kerusakan')"
                        >
                            <img
                                src="{{ $fotoKerusakanUrl }}"
                                alt="Foto Kerusakan"
                                class="w-full h-64 object-cover rounded-lg border border-gray-200 hover:opacity-95 transition"
                            >
                        </button>

                    @else

                        <div class="w-full h-48 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                            Tidak ada foto
                        </div>

                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                Alamat
                            </p>

                            <p class="text-gray-900">
                                {{ $laporan->alamat }}
                            </p>

                        </div>

                        <div>

                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                Koordinat
                            </p>

                            <p class="text-gray-900">
                                {{ $laporan->latitude }},
                                {{ $laporan->longitude }}
                            </p>

                        </div>

                    </div>

                    <div>

                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                            Keterangan
                        </p>

                        <p class="text-gray-900 leading-relaxed">
                            {{ $laporan->keterangan }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                            Tanggal Laporan
                        </p>

                        <p class="text-gray-900">
                            {{ $laporan->created_at->format('d F Y, H:i') }} WIB
                        </p>

                    </div>

                </div>

            </div>

            {{-- Riwayat Status --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

                <div class="px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Riwayat Status
                    </h2>

                </div>

                <div class="px-6 py-4">

                    @if($laporan->statuses->count() > 0)

                        <div class="space-y-6">

                            @foreach($laporan->statuses->sortByDesc('created_at') as $s)

                            @php

                                $dotColors = [

                                    'diterima' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                    'diproses' => 'bg-amber-100 text-amber-600 border-amber-200',
                                    'selesai'  => 'bg-purple-100 text-purple-600 border-purple-200',
                                    'ditolak'  => 'bg-red-100 text-red-600 border-red-200',

                                ][$s->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';

                            @endphp

                            <div class="flex gap-4 relative">

                                <div class="w-8 h-8 rounded-full border flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $dotColors }}">
                                    ●
                                </div>

                                <div class="flex-1 min-w-0">

                                    <div class="flex items-center justify-between gap-2">

                                        <span class="font-semibold text-sm text-gray-900">
                                            {{ ucfirst($s->status) }}
                                        </span>

                                        <span class="text-xs text-gray-400 whitespace-nowrap">
                                            {{ $s->created_at->format('d M Y, H:i') }}
                                        </span>

                                    </div>

                                    <p class="text-xs text-gray-500 mt-0.5">
                                        oleh {{ $s->admin->name ?? 'Admin' }}
                                    </p>

                                    @if($s->keterangan)

                                        <div class="mt-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700">

                                            {{ $s->keterangan }}

                                        </div>

                                    @endif

                                    @if($s->foto_perbaikan)

                                        <div class="mt-3">

                                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                                Foto Perbaikan
                                            </p>

                                            @php
                                                $fotoPerbaikanUrl = asset('storage/' . $s->foto_perbaikan);
                                            @endphp

                                            <button
                                                type="button"
                                                class="w-full"
                                                onclick="showImageModal('{{ $fotoPerbaikanUrl }}', 'Foto Perbaikan')"
                                            >
                                                <img
                                                    src="{{ $fotoPerbaikanUrl }}"
                                                    alt="Foto Perbaikan"
                                                    class="w-full h-48 object-cover rounded-lg border border-gray-200 hover:opacity-95 transition"
                                                >
                                            </button>

                                        </div>

                                    @endif

                                </div>

                            </div>

                            @endforeach

                        </div>

                    @else

                        <p class="text-gray-500 text-sm text-center py-6">
                            Belum ada update status untuk laporan ini.
                        </p>

                    @endif

                </div>

            </div>

        </div>

        {{-- ================= KOLOM KANAN ================= --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Update Status --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-6">

                <div class="px-6 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">
                        Update Status Laporan
                    </h2>

                </div>

                <div class="px-6 py-4">

                    <form
                        action="{{ route('admin.laporan.update', $laporan->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf
                        @method('PUT')

                        <div class="mb-4">

                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Status Baru
                            </label>

                            <select
                                name="status"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                            >

                                <option value="">-- Pilih Status --</option>

                                <option value="diterima">
                                    ✓ Diterima
                                </option>

                                <option value="diproses">
                                    ⚙ Diproses
                                </option>

                                <option value="selesai">
                                    ★ Selesai
                                </option>

                                <option value="ditolak">
                                    ✕ Ditolak
                                </option>

                            </select>

                        </div>

                        <div class="mb-4">

                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Keterangan
                            </label>

                            <textarea
                                name="keterangan"
                                rows="4"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 resize-y"
                                placeholder="Masukkan keterangan..."
                            ></textarea>

                        </div>

                        <div class="mb-5">

                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                Foto Perbaikan
                            </label>

                            <input
                                type="file"
                                name="foto_perbaikan"
                                accept="image/*"
                                class="block w-full text-sm text-gray-500"
                            >

                        </div>

                        <button
                            type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm px-5 py-3 transition"
                        >
                            Simpan Perubahan
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

{{-- =========================
     IMAGE MODAL (LIGHTBOX)
========================= --}}
<div
    id="imageModal"
    class="fixed inset-0 z-50 hidden"
    aria-hidden="true"
>
    <div
        class="absolute inset-0 bg-black/60"
        onclick="hideImageModal()"
    ></div>

    <div class="relative w-full h-full flex items-center justify-center p-4">
        <div class="w-full max-w-5xl bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <p id="imageModalTitle" class="text-sm font-semibold text-gray-900">
                    —
                </p>
                <button
                    type="button"
                    class="text-gray-500 hover:text-gray-700"
                    onclick="hideImageModal()"
                    aria-label="Tutup"
                >
                    ✕
                </button>
            </div>

            <div class="p-4 bg-gray-50">
                <img
                    id="imageModalImg"
                    src=""
                    alt=""
                    class="w-full max-h-[75vh] object-contain rounded-lg bg-white border border-gray-200"
                >
            </div>
        </div>
    </div>
</div>

<script>
    function showImageModal(src, title) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('imageModalImg');
        const modalTitle = document.getElementById('imageModalTitle');

        img.src = src;
        modalTitle.textContent = title || '—';

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function hideImageModal() {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('imageModalImg');

        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        // clear src to avoid broken-image on next open if any
        img.src = img.src;
    }

    // close on ESC
    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('imageModal');
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideImageModal();
        }
    });
</script>

@endsection
