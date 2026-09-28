@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ================= HEADER =================
         Versi lama memakai variabel $total / $diterima / $diproses /
         $selesai yang controller sudah tidak kirim lagi -> halaman ini
         selalu error "Undefined variable". Sekarang semua angka dibaca
         dari $stats dan $stats wajib ada sebelum dirender. --}}
    @php
        $stats = $stats ?? [
            'total' => 0, 'baru' => 0, 'diterima' => 0, 'diproses' => 0,
            'selesai' => 0, 'ditolak' => 0, 'dikerjakan' => 0,
            'rasioSelesai' => 0, 'rasioDikerjakan' => 0,
        ];
        $antrean = $antrean ?? collect();
        $perluTindakan = $perluTindakan ?? 0;
    @endphp

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Dashboard Admin
        </h1>

        <p class="mt-1 text-gray-600">
            {{-- Angka di bawah adalah total SEJAK AWAL, bukan per bulan.
                 `LaporanStats::ringkasan()` dipanggil tanpa argumen
                 tahun, jadi teks lama "Ringkasan laporan per
                 September 2026" menyebut periode yang salah --
                 kebetulan terlihat benar sekarang, tapi tidak akan
                 pernah benar begitu isinya bertambah. --}}
            Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan
            seluruh laporan yang masuk.
        </p>
    </div>

    {{-- ================= PERLU TINDAKAN ================= --}}
    @if ($perluTindakan > 0)
        <div
            class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-300 bg-amber-50 p-5"
            role="alert"
        >
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-6 w-6 shrink-0 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <div>
                    <p class="font-semibold text-amber-900">
                        {{ $perluTindakan }} laporan menunggu lebih dari 3 hari
                    </p>
                    <p class="text-sm text-amber-800">
                        Laporan ini statusnya masih "Diterima" atau "Diproses".
                        Kalau masih di lapangan, tambahkan catatan pembaruan supaya
                        pelapor tidak merasa tidak ada kabar.
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.laporan.index', ['status' => 'diterima']) }}" class="btn btn-primary shrink-0">
                Tangani sekarang
            </a>
        </div>
    @endif

    {{-- ================= KARTU STATISTIK ================= --}}
    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">

        @php
            /*
             * `text-{{ $warna }}` TIDAK akan pernah jadi CSS. Tailwind
             * memindai file sebagai teks, bukan mengeksekusi Blade,
             * jadi kelas yang dirangkai dari variabel tidak pernah
             * muncul di hasil build dan angka di kartunya jadi tak
             * berwarna. Karena itu warna ditulis utuh di sini.
             */
            $kartu = [
                ['Total Laporan', $stats['total'], 'admin.laporan.index', [], 'text-accent-700'],
                ['Diterima', $stats['diterima'], 'admin.laporan.index', ['status' => 'diterima'], 'text-primary-700'],
                ['Diproses', $stats['diproses'], 'admin.laporan.index', ['status' => 'diproses'], 'text-amber-700'],
                ['Selesai', $stats['selesai'], 'admin.laporan.index', ['status' => 'selesai'], 'text-emerald-700'],
            ];
        @endphp

        @foreach ($kartu as [$judul, $angka, $rute, $param, $warna])
            <a
                href="{{ route($rute, $param) }}"
                class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-accent-300 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-700"
            >
                <p class="text-sm font-medium text-gray-500">{{ $judul }}</p>
                <p class="mt-1 text-3xl font-bold {{ $warna }}">{{ $angka }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

          {{-- ================= ANTREAN TERBARU ================= --}}
          {{-- `min-w-0` WAJIB ada di sini. Item grid punya `min-width: auto`
               secara bawaan, jadi ukurannya ikut melebar mengikuti isi
               terburuknya. Tanpa itu, di layar 375px kolom "Laporan Terbaru"
               memaksa halaman jadi bisa di-scroll ke samping (scrollWidth
               442px vs viewport 375px) dan seluruh konten bergeser keluar
               layar. `--}}
          <div class="lg:col-span-2 min-w-0">

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                    <h2 class="text-lg font-bold text-gray-900">Laporan Terbaru</h2>
                    <a href="{{ route('admin.laporan.index') }}" class="inline-block py-1 text-sm font-semibold text-accent-700 hover:underline">
                        Lihat semua
                    </a>
                </div>

                @if ($antrean->isEmpty())
                    <p class="p-8 text-center text-sm text-gray-500">
                        Belum ada laporan masuk.
                    </p>
                @else
                    <ul class="divide-y divide-gray-100" role="list">
                        @foreach ($antrean as $laporan)
                            @php $status = $laporan->latestStatus?->status; @endphp

                            <li>
                                <a
                                    href="{{ route('admin.laporan.show', $laporan->id) }}"
                                    class="flex items-start gap-4 p-4 transition hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-accent-700"
                                >
                                    @if ($laporan->foto)
                                        <img
                                            src="{{ asset('storage/' . $laporan->foto) }}"
                                            alt=""
                                            loading="lazy"
                                            class="h-14 w-14 shrink-0 rounded-xl object-cover"
                                        >
                                    @else
                                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-gray-100 text-xs text-gray-500">
                                            Tanpa foto
                                        </span>
                                    @endif

                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center gap-2">
                                            <x-status-badge :status="$status" />
                                            <span class="font-mono text-xs text-gray-500">{{ $laporan->nomor_referensi }}</span>
                                        </span>

                                        <span class="mt-1 block truncate text-sm font-semibold text-gray-900">
                                            {{ $laporan->alamat }}
                                        </span>

                                        <span class="block text-xs text-gray-500">
                                            {{ $laporan->user?->name ?? 'Akun pelapor tidak tersedia' }}
                                            &middot; {{ $laporan->created_at?->format('d M Y, H:i') }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- ================= MENU ================= --}}
        <div class="space-y-4">

            @php
                $menu = [
                    ['Manajemen Laporan', 'Kelola dan verifikasi semua laporan masuk', 'admin.laporan.index'],
                    ['Manajemen FAQ', 'Kelola pertanyaan yang tampil di pusat bantuan', 'admin.manajemen-faq.index'],
                    ['Statistik & Grafik', 'Ringkasan laporan per bulan dan per tahun', 'admin.statistik.index'],
                ];
            @endphp

            @foreach ($menu as [$judul, $ketika, $rute])
                <a
                    href="{{ route($rute) }}"
                    class="block rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-accent-300 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-700"
                >
                    <p class="font-bold text-gray-900">{{ $judul }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ $ketika }}</p>
                </a>
            @endforeach

            {{-- Menu akun hanya relevan untuk super admin. Rutenya di
                 prefix superadmin, bukan admin, jadi keduanya dicek
                 supaya tidak pernah menghasilkan 404. --}}
            @if (auth()->user()->role === 'super_admin')
                <a
                    href="{{ route('superadmin.accounts.index') }}"
                    class="block rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-accent-300 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-700"
                >
                    <p class="font-bold text-gray-900">Manajemen Akun</p>
                    <p class="mt-1 text-sm text-gray-600">Kelola admin, petugas, dan pelapor</p>
                </a>
            @endif

            {{-- Ringkasan tambahan yang tidak muat di 4 kartu atas --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-gray-900">Ringkasan Lainnya</h2>

                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Ditolak</dt>
                        <dd class="font-semibold text-[var(--danger)]">{{ $stats['ditolak'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Belum ada status</dt>
                        <dd class="font-semibold text-gray-900">{{ $stats['baru'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Rasio selesai</dt>
                        <dd class="font-semibold text-emerald-700">{{ $stats['rasioSelesai'] }}%</dd>
                    </div>
                </dl>
            </div>

        </div>
    </div>

</div>
@endsection
