@extends('layouts.app')

@section('title', 'Riwayat Laporan')
@section('deskripsi', 'Pantau status dan riwayat penanganan semua laporan yang pernah kamu kirim.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endpush

@section('content')

<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

    {{-- ============ HEADER ============ --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Riwayat Laporan</h1>
            <p class="mt-1 text-sm text-slate-600">
                Total {{ $ringkasan['total'] }} laporan yang pernah kamu kirim.
            </p>
        </div>

        <a href="{{ route('laporan.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Buat Laporan Baru
        </a>
    </div>

    {{-- ============ RINGKASAN ============
         Dipakai juga sebagai filter, jadi ini bukan angka mati:
         klik untuk menyaring daftar di bawahnya. --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5 mb-6">
        @php
            $kartu = [
                '' => ['Semua', $ringkasan['total'], 'text-slate-900', 'bg-white', 'border-slate-200'],
                'diterima' => ['Diterima', $ringkasan['diterima'], 'text-blue-800', 'bg-blue-50', 'border-blue-200'],
                'diproses' => ['Diproses', $ringkasan['diproses'], 'text-amber-900', 'bg-amber-50', 'border-amber-200'],
                'selesai' => ['Selesai', $ringkasan['selesai'], 'text-emerald-800', 'bg-emerald-50', 'border-emerald-200'],
                'ditolak' => ['Ditolak', $ringkasan['ditolak'], 'text-[var(--ditolak-ink)]', 'bg-[var(--ditolak-tint)]', 'border-[var(--line)]'],
            ];
            $statusAktif = (string) request('status', '');
        @endphp

        @foreach ($kartu as $nilai => [$judul, $jumlah, $warna, $latar, $garis])
            @php $terpilih = $statusAktif === $nilai; @endphp
            <a
                href="{{ $nilai === ''
                    ? route('laporan.my-report', array_filter(['q' => request('q')]))
                    : route('laporan.my-report', array_filter(['status' => $nilai, 'q' => request('q')])) }}"
                @if ($terpilih) aria-current="page" @endif
                class="rounded-2xl border px-4 py-3 transition hover:shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 {{ $garis }} {{ $latar }} {{ $terpilih ? 'ring-2 ring-teal-700' : '' }}"
            >
                <span class="block text-2xl font-bold {{ $warna }}">{{ $jumlah }}</span>
                <span class="block text-xs font-semibold text-slate-700 mt-0.5">{{ $judul }}</span>
            </a>
        @endforeach
    </div>

    {{-- ============ PENCARIAN ============ --}}
    <form method="GET" action="{{ route('laporan.my-report') }}" class="mb-6 flex flex-wrap items-end gap-3">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="min-w-0 flex-1">
            <label for="q" class="block text-sm font-semibold text-slate-800 mb-1">
                Cari laporan
            </label>
            <input
                type="search"
                name="q"
                id="q"
                value="{{ request('q') }}"
                placeholder="Alamat, keterangan, atau nomor SRJ-2026-1"
                maxlength="100"
                class="field-input"
            >
        </div>

        <button type="submit" class="btn btn-secondary">Cari</button>

        @if (request('q') || request('status'))
            <a href="{{ route('laporan.my-report') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    {{-- ============ DAFTAR LAPORAN ============ --}}
    @if ($reports->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <svg class="mx-auto w-12 h-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <h2 class="mt-4 text-lg font-semibold text-slate-900">
                @if (request('q') || request('status'))
                    Tidak ada laporan yang cocok
                @else
                    Belum ada laporan
                @endif
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                @if (request('q') || request('status'))
                    Coba kata kunci lain atau reset filternya.
                @else
                    Mulai dengan mengirim laporan kerusakan jalan pertama kamu.
                @endif
            </p>

            <a
                href="{{ (request('q') || request('status')) ? route('laporan.my-report') : route('laporan.create') }}"
                class="btn btn-primary mt-6 inline-flex"
            >
                {{ (request('q') || request('status')) ? 'Reset filter' : 'Buat Laporan' }}
            </a>
        </div>
    @else
        <ul class="space-y-3" role="list">
            @foreach ($reports as $r)
                @php
                    $status = $r->latestStatus?->status;
                    $peta = \App\Support\StatusPeta::get($status);
                    $petaAlur = \App\Support\StatusPeta::alur();
                    $tahap = $status ? array_search($status, $petaAlur, true) : false;
                @endphp

                <li class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-teal-700/40 hover:shadow-sm sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row">

                        {{-- Foto --}}
                        <div class="flex-shrink-0">
                            @if ($r->foto)
                                <img
                                    src="{{ asset('storage/' . $r->foto) }}"
                                    alt="Foto kerusakan di {{ $r->alamat }}"
                                    loading="lazy"
                                    class="h-28 w-full rounded-xl object-cover sm:h-24 sm:w-32"
                                >
                            @else
                                <div class="grid h-28 w-full place-items-center rounded-xl bg-slate-100 text-xs text-slate-500 sm:h-24 sm:w-32">
                                    Tanpa foto
                                </div>
                            @endif
                        </div>

                        {{-- Ringkasan --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$status" />
                                <span class="font-mono text-xs text-slate-500">{{ $r->nomor_referensi }}</span>
                            </div>

                            <h2 class="mt-2 text-base font-semibold text-slate-900">
                                {{ \Illuminate\Support\Str::limit($r->alamat, 90) }}
                            </h2>

                            <p class="mt-1 line-clamp-2 text-sm text-slate-600">
                                {{ \Illuminate\Support\Str::limit($r->keterangan, 160) }}
                            </p>

                            {{-- Progres --}}
                            @if ($tahap !== false)
                                <ol class="mt-3 flex items-center gap-1.5" role="list" aria-label="Progres penanganan">
                                    @foreach ($petaAlur as $i => $tahapStatus)
                                        @php
                                            $sudah = $i <= $tahap;
                                            $warnaTitik = $sudah
                                                ? \App\Support\StatusPeta::get($tahapStatus)['dot']
                                                : 'bg-slate-300';
                                        @endphp
                                        <li class="flex items-center gap-1.5">
                                            <span class="h-2 w-2 rounded-full {{ $warnaTitik }}"
                                                  aria-hidden="true"></span>
                                            <span class="text-xs {{ $sudah ? 'font-semibold text-slate-700' : 'text-slate-500' }}">
                                                {{ \App\Support\StatusPeta::get($tahapStatus)['label'] }}
                                            </span>
                                            @if (! $loop->last)
                                                <span class="h-px w-4 {{ $sudah ? 'bg-slate-400' : 'bg-slate-200' }}" aria-hidden="true"></span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            @elseif ($status === 'ditolak')
                                <p class="mt-3 text-xs text-[var(--ditolak-ink)]">
                                    Laporan ini ditolak. Buka detailnya untuk melihat alasannya.
                                </p>
                            @endif

                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-xs text-slate-500">
                                    Dikirim {{ $r->created_at?->format('d M Y, H:i') }}
                                    @if ($r->latestStatus?->created_at)
                                        &middot; Diperbarui {{ $r->latestStatus->created_at->format('d M Y, H:i') }}
                                    @endif
                                </p>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-dialog="{{ $r->id }}"
                                >
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- ============ PAGINATION ============ --}}
        @if ($reports->hasPages())
            <nav class="mt-8" role="navigation" aria-label="Navigasi halaman laporan">
                {{ $reports->onEachSide(1)->links() }}
            </nav>
        @endif
    @endif

</div>

{{-- ============ DIALOG DETAIL ============
     Elemen <dialog> asli, bukan div popup buatan sendiri. Versi lama
     memakai `onclick` di <div> sehingga tidak bisa dibuka dengan
     keyboard, tidak terkunci fokus, dan tidak menutup dengan Esc. --}}
@foreach ($reports as $r)
    @php
        $status = $r->latestStatus?->status;
        $koordinat = $r->latitude && $r->longitude;
        $tautanPeta = $koordinat
            ? 'https://www.google.com/maps/search/?api=1&query=' . $r->latitude . ',' . $r->longitude
            : null;
    @endphp

    <dialog id="detail-{{ $r->id }}" class="detail-dialog" aria-labelledby="detail-judul-{{ $r->id }}">
        <div class="detail-panel" role="document">

            <div class="detail-kepala">
                <div class="min-w-0">
                    <p class="font-mono text-xs text-slate-500">{{ $r->nomor_referensi }}</p>
                    <h2 id="detail-judul-{{ $r->id }}" class="truncate text-lg font-bold text-slate-900">
                        {{ $r->alamat }}
                    </h2>
                </div>

                <button
                    type="button"
                    class="detail-tutup"
                    data-tutup="{{ $r->id }}"
                    aria-label="Tutup detail laporan"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="detail-isi">

                @if ($r->foto)
                    <img
                        src="{{ asset('storage/' . $r->foto) }}"
                        alt="Foto kerusakan di {{ $r->alamat }}"
                        class="detail-foto"
                    >
                @endif

                <dl class="detail-daftar">
                    <div>
                        <dt>Status saat ini</dt>
                        <dd><x-status-badge :status="$status" /></dd>
                    </div>

                    <div>
                        <dt>Nama pelapor</dt>
                        <dd>{{ $r->nama_pelapor ?: '-' }}</dd>
                    </div>

                    <div>
                        <dt>Dikirim pada</dt>
                        <dd>{{ $r->created_at?->format('d F Y, H:i') }}</dd>
                    </div>

                    <div>
                        <dt>Lokasi</dt>
                        <dd>
                            @if ($tautanPeta)
                                <a href="{{ $tautanPeta }}" target="_blank" rel="noopener noreferrer" class="tautan-peta">
                                    {{ number_format($r->latitude, 5) }}, {{ number_format($r->longitude, 5) }}
                                    <span class="sr-only">(buka di Google Maps, tab baru)</span>
                                </a>
                            @else
                                <span class="text-slate-500">Tidak tersedia</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="detail-bagian">
                    <h3>Keterangan kamu</h3>
                    <p class="whitespace-pre-line">{{ $r->keterangan }}</p>
                </div>

                <div class="detail-bagian">
                    <h3>Riwayat penanganan</h3>

                    <ol class="timeline" role="list">
                        @forelse ($r->statuses as $s)
                            @php $petaStatus = \App\Support\StatusPeta::get($s->status); @endphp
                            <li>
                                <span class="timeline-titik {{ $petaStatus['dot'] }}" aria-hidden="true"></span>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-status-badge :status="$s->status" />
                                        <span class="text-xs text-slate-500">
                                            {{ $s->created_at?->format('d M Y, H:i') }}
                                        </span>
                                    </div>

                                    @if ($s->keterangan)
                                        <p class="mt-1.5 text-sm text-slate-700">{{ $s->keterangan }}</p>
                                    @endif

                                    @if ($s->admin)
                                        <p class="mt-1 text-xs text-slate-500">
                                            oleh {{ $s->admin->name }}
                                        </p>
                                    @elseif ($s->user_id)
                                        <p class="mt-1 text-xs text-slate-500">
                                            oleh akun yang sudah dinonaktifkan
                                        </p>
                                    @endif

                                    @if ($s->foto_perbaikan)
                                        <img
                                            src="{{ asset('storage/' . $s->foto_perbaikan) }}"
                                            alt="Foto perbaikan"
                                            loading="lazy"
                                            class="mt-2.5 h-32 rounded-lg object-cover"
                                        >
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Belum ada catatan penanganan.</li>
                        @endforelse
                    </ol>
                </div>
            </div>

        </div>
    </dialog>
@endforeach

@push('scripts')
<script>
    /* Pembuka & penutup dialog. Letakkan di sini, bukan inline onclick,
       supaya tidak ada handler yang menyalin diri sendiri tiap render. */
    (function () {
        var terakhirFokus = null;

        document.addEventListener('click', function (event) {
            var buka = event.target.closest('[data-dialog]');
            var tutup = event.target.closest('[data-tutup]');

            if (buka) {
                var dialog = document.getElementById('detail-' + buka.dataset.dialog);

                if (dialog && typeof dialog.showModal === 'function') {
                    terakhirFokus = buka;
                    dialog.showModal();
                    document.body.style.overflow = 'hidden';
                }
            }

            if (tutup) {
                var asal = document.getElementById('detail-' + tutup.dataset.tutup);

                if (asal && asal.open) asal.close();
            }
        });

        /* Klik di area gelap di luar panel ikut menutup. */
        document.addEventListener('click', function (event) {
            if (event.target.tagName !== 'DIALOG') return;

            event.target.close();
        });

        document.querySelectorAll('dialog').forEach(function (dialog) {
            dialog.addEventListener('close', function () {
                document.body.style.overflow = '';
                if (terakhirFokus) {
                    terakhirFokus.focus();
                    terakhirFokus = null;
                }
            });
        });
    })();
</script>
@endpush

@endsection
