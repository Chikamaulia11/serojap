@extends('layouts.superadmin')

@section('title', 'Manajemen Akun — SEROJAP')

@section('content')

@php
    /*
     * PENCARIAN DAN FILTER DIPINDAH KE SERVER.
     *
     * Sebelumnya searching dan filtering dilakukan di JavaScript dengan
     * menyaring DOM. Begitu controller mulai mem-paginate daftar akun,
     * cara itu langsung rusak: `display: none` hanya menyembunyikan baris
     * di halaman yang sedang terbuka, jadi mengetik email akun di
     * halaman 3 tidak akan pernah menemukan apa pun. Filternya juga
     * membuat angka kartu ringkasan ikut berubah, padahal angka itu
     * harusnya selalu menunjukkan seluruh data.
     *
     * Sekarang query string yang sama (`search`, `role`, `status`)
     * ditangani controller, dan kartu ringkasan memakai angka
     * `$ringkasan` yang dihitung terpisah dari hasil filter.
     */
    $ringkasan = $ringkasan ?? ['admin' => 0, 'pelapor' => 0, 'aktif' => 0, 'nonaktif' => 0, 'total' => 0];
    $totalAdmin = $ringkasan['admin'];
    $totalPelapor = $ringkasan['pelapor'];
    $totalAkun = $ringkasan['total'];

    $filterAktif = (string) request('role', '');
    $statusAktif = (string) request('status', '');
    $kataKunci = (string) request('search', '');
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if(session('success'))
    <div id="swal-success" data-message="{{ session('success') }}" class="hidden"></div>
@endif

@if(session('error'))
    <div id="swal-error" data-message="{{ session('error') }}" class="hidden"></div>
@endif

<style>
    .account-page {
        animation: pageFade .45s ease both;
    }

    @keyframes pageFade {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .account-header {
        background:
            linear-gradient(135deg, rgba(255,255,255,.98), rgba(241,247,255,.98)),
            radial-gradient(circle at right top, rgba(38,87,193,.13), transparent 34%);
    }

    .stat-card,
    .account-list-card,
    .account-item-card {
        transition: .22s ease;
    }

    .stat-card:hover,
    .account-item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(15,23,42,.07);
    }

    .account-search {
        background: rgba(255,255,255,.88);
    }

    .swal2-popup-custom {
        border-radius: 20px !important;
        padding: 2rem !important;
        font-family: inherit !important;
    }

    .swal2-title-custom {
        font-size: 1.4rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }

    .swal2-text-custom {
        font-size: 0.95rem !important;
        color: #6b7280 !important;
    }

    .swal2-confirm-custom {
        border-radius: 12px !important;
        padding: 0.6rem 2rem !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        box-shadow: none !important;
        border: none !important;
    }

    .swal2-confirm-success {
        background-color: #2657c1 !important;
        color: #fff !important;
    }

    .swal2-confirm-error {
        background-color: #dc2626 !important;
        color: #fff !important;
    }

    .swal2-backdrop-custom {
        backdrop-filter: blur(4px) !important;
        background: rgba(0, 0, 0, 0.35) !important;
    }
</style>

<div class="account-page max-w-6xl mx-auto space-y-5">

    <!-- HEADER -->
    <section class="account-header border border-slate-200 rounded-3xl p-6 shadow-sm relative overflow-hidden">

        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-blue-100/40"></div>
        <div class="absolute right-10 bottom-0 w-24 h-24 rounded-full bg-teal-100/40"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

            <div class="max-w-2xl">

                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#2657c1] text-xs font-extrabold mb-4">
                    <i class="mdi mdi-shield-account-outline text-base"></i>
                    Kontrol Akun Sistem
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                    Manajemen Akun
                </h1>

                <p class="text-sm md:text-base text-slate-500 mt-2 leading-relaxed">
                    Kelola akun admin dan pelapor dalam satu halaman. Admin dibuat melalui panel super admin,
                    sedangkan pelapor tetap dapat registrasi sendiri melalui halaman publik.
                </p>

            </div>

            <a href="{{ route('superadmin.accounts.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#2657c1] text-white text-sm font-extrabold hover:bg-[#1f4674] transition shadow-lg shadow-blue-500/20">
                <i class="mdi mdi-account-plus-outline text-lg"></i>
                Tambah Akun
            </a>

        </div>

    </section>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="stat-card bg-white border border-slate-200 rounded-3xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em]">
                        Total Admin
                    </p>

                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                        {{ $totalAdmin }}
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                        Admin dibuat dan dikelola super admin.
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-tie-outline text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white border border-slate-200 rounded-3xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em]">
                        Total Pelapor
                    </p>

                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                        {{ $totalPelapor }}
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                        Pelapor bisa registrasi mandiri.
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-group-outline text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white border border-slate-200 rounded-3xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em]">
                        Total Akun
                    </p>

                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                        {{ $totalAkun }}
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                        Seluruh akun yang dapat dikelola.
                    </p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-shield-check-outline text-2xl"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- LIST -->
    <section class="account-list-card bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">

        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70">

            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Daftar Akun
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Data admin dan pelapor yang terdaftar pada sistem.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">

                    {{-- Filter dikirim sebagai query string supaya
                         controller yang menyaring, bukan JavaScript.
                         searching lewat DOM hanya bisa melihat baris
                         di halaman yang sedang terbuka. --}}
                    <form method="GET"
                          action="{{ route('superadmin.accounts.index') }}"
                          class="flex flex-col sm:flex-row sm:flex-wrap gap-3">

                        <div class="account-search flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 min-w-[240px]">
                            <i class="mdi mdi-magnify text-slate-400 text-lg" aria-hidden="true"></i>

                            <label for="accountSearch" class="sr-only">Cari nama, email, atau posisi</label>

                            <input type="search"
                                   name="search"
                                   id="accountSearch"
                                   value="{{ $kataKunci }}"
                                   placeholder="Cari nama, email, posisi..."
                                   class="w-full bg-transparent border-0 outline-none focus:ring-0 text-sm text-slate-700 placeholder:text-slate-400 p-0">
                        </div>

                        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter berdasarkan role">

                            @php
                                $pilihanRole = [
                                    '' => 'Semua',
                                    'admin' => 'Admin',
                                    'pelapor' => 'Pelapor',
                                ];
                            @endphp

                            @foreach ($pilihanRole as $nilai => $judul)
                                <button type="submit"
                                        name="role"
                                        value="{{ $nilai }}"
                                        @if ($filterAktif === $nilai) aria-current="page" @endif
                                        class="account-filter-btn px-4 py-2.5 rounded-2xl text-xs font-extrabold transition
                                               {{ $filterAktif === $nilai
                                                    ? 'bg-[#2657c1] text-white'
                                                    : 'bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                                    {{ $judul }}
                                </button>
                            @endforeach
                        </div>

                    </form>

                </div>

            </div>

            {{-- Filter status aktif + hasil pencarian --}}
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('superadmin.accounts.index') }}" class="flex flex-wrap gap-2" role="group" aria-label="Filter berdasarkan status">
                    @if ($kataKunci)
                        <input type="hidden" name="search" value="{{ $kataKunci }}">
                    @endif
                    @if ($filterAktif)
                        <input type="hidden" name="role" value="{{ $filterAktif }}">
                    @endif

                    @php
                        $pilihanStatus = [
                            '' => 'Semua status',
                            'aktif' => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                        ];
                    @endphp

                    @foreach ($pilihanStatus as $nilai => $judul)
                        <button type="submit"
                                name="status"
                                value="{{ $nilai }}"
                                @if ($statusAktif === $nilai) aria-current="true" @endif
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition
                                       {{ $statusAktif === $nilai
                                            ? 'bg-slate-900 text-white'
                                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $judul }}
                        </button>
                    @endforeach
                </form>

                <p class="text-sm text-slate-500" role="status" aria-live="polite">
                    Menampilkan {{ $users->count() }} dari {{ $users->total() }} akun
                    @if ($kataKunci)
                        <span class="font-semibold">untuk "{{ $kataKunci }}"</span>
                    @endif
                </p>
            </div>

        </div>

        <div class="p-5">

            <div class="space-y-3" id="accountGrid">

                {{-- `$users` adalah Paginator, bukan Collection. --}}
                @forelse($users as $account)

                    @php
                        $terhapus = $account->trashed();
                        $aktif = ! $terhapus && (bool) $account->is_active;
                    @endphp

                    <div class="account-item account-item-card border rounded-3xl p-4 bg-white
                                {{ $terhapus ? 'border-rose-200 bg-rose-50/40' : 'border-slate-200' }}">

                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                            <div class="flex items-center gap-4 min-w-0">

                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#2657c1] to-[#226d71] text-white flex items-center justify-center font-extrabold text-sm flex-shrink-0 overflow-hidden shadow-md shadow-blue-100">
                                    @if($account->foto_profil)
                                        <img src="{{ asset('storage/' . $account->foto_profil) }}"
                                             alt="Foto Profil"
                                             class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($account->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>

                                <div class="min-w-0">

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-extrabold text-slate-900 leading-tight truncate">
                                            {{ $account->name }}
                                        </h3>

                                        @if($account->role === 'admin')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700">
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700">
                                                Pelapor
                                            </span>
                                        @endif

                                        {{-- Status akun. Tanpa ini, akun
                                             nonaktif terlihat identik
                                             dengan yang aktif; yang
                                             dinonaktifkan masih bisa
                                             login kalau ada bug di
                                             middleware, dan super
                                             admin tidak punya cara
                                             untuk melihatnya. --}}
                                        @if ($aktif)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700">
                                                {{ $terhapus ? 'Dinonaktifkan' : 'Tidak Aktif' }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-slate-500 break-all">
                                        {{ $account->email }}
                                    </p>

                                    <p class="mt-1 text-[11px] text-slate-400 font-semibold">
                                        Dibuat: {{ $account->created_at?->format('d M Y') ?? '-' }}
                                    </p>

                                </div>

                            </div>

                            <div class="flex flex-wrap gap-2 flex-shrink-0 lg:justify-end">

                                @if ($terhapus)
                                    {{-- Route restore pakai `->withTrashed()`.
                                         Tanpa tombol ini, fitur pulihkan
                                         akun tidak akan pernah bisa
                                         dipakai dari UI. --}}
                                    <form action="{{ route('superadmin.accounts.restore', $account->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-extrabold rounded-2xl border border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">
                                            <i class="mdi mdi-backup-restore" aria-hidden="true"></i>
                                            Aktifkan Kembali
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('superadmin.accounts.edit', $account->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-extrabold rounded-2xl border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                        <i class="mdi mdi-pencil-outline" aria-hidden="true"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('superadmin.accounts.destroy', $account->id) }}"
                                          method="POST"
                                          onsubmit="return confirmDelete(event, this, @js($account->name), @js($account->role));">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-extrabold rounded-2xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
                                            <i class="mdi mdi-trash-can-outline" aria-hidden="true"></i>
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    {{-- Bedakan "belum ada akun sama sekali" dari
                         "filter tidak cocok". Tanpa pemisahan ini
                         super admin bisa salah mengira sistemnya
                         kosong padahal datanya ada di halaman lain. --}}
                    <div class="text-center py-14">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 flex items-center justify-center text-3xl mb-4" aria-hidden="true">
                            {{ $kataKunci || $filterAktif || $statusAktif ? '🔎' : '👥' }}
                        </div>

                        <div class="font-extrabold text-slate-900">
                            {{ ($kataKunci || $filterAktif || $statusAktif) ? 'Tidak ada data yang cocok' : 'Belum ada akun' }}
                        </div>

                        <div class="text-slate-500 text-sm mt-1">
                            {{ ($kataKunci || $filterAktif || $statusAktif)
                                ? 'Coba ubah filter atau kata pencarian.'
                                : 'Gunakan tombol tambah akun untuk membuat data baru.' }}
                        </div>

                        @if ($kataKunci || $filterAktif || $statusAktif)
                            <a href="{{ route('superadmin.accounts.index') }}"
                               class="inline-flex items-center justify-center mt-5 px-4 py-2.5 rounded-2xl bg-slate-900 text-white text-xs font-extrabold">
                                Reset filter
                            </a>
                        @endif
                    </div>

                @endforelse

            </div>

            {{-- Paginasi. Query string sudah ikut terbawa lewat
                 `withQueryString()` di controller, jadi filter tidak
                 hilang saat pindah halaman. --}}
            @if ($users->hasPages())
                <nav class="px-5 py-4 border-t border-slate-200" role="navigation" aria-label="Navigasi halaman akun">
                    {{ $users->onEachSide(1)->links() }}
                </nav>
            @endif

        </div>

    </section>

</div>

<script>
    function confirmDelete(event, formEl, accountName, role) {
        event.preventDefault();

        const roleLabel = role === 'admin' ? 'admin' : 'pelapor';

        Swal.fire({
            icon: 'warning',
            title: 'Nonaktifkan Akun?',
            /* Kotak dialog ini menjelaskan apa yang benar-benar terjadi.
               Tombolnya sudah disebut "Nonaktifkan" dan datanya hanya
               di-soft-delete, jadi kalimat "akan dihapus dari sistem"
               semuanya tidak benar: laporan dan riwayat statusnya
               tetap utuh dan akun bisa diaktifkan kembali. */
            html: `Akun <b>${accountName}</b> (<b>${roleLabel}</b>) tidak bisa lagi masuk atau mengirim laporan. `
                + `Laporan dan riwayat statusnya tetap tersimpan, dan akun bisa diaktifkan kembali kapan saja.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Nonaktifkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-text-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-error',
                cancelButton: 'swal2-confirm-custom',
                backdrop: 'swal2-backdrop-custom',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                formEl.submit();
            }
        });

        return false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const successEl = document.getElementById('swal-success');
        const errorEl = document.getElementById('swal-error');

        if (successEl) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: successEl.dataset.message,
                confirmButtonText: 'Oke',
                confirmButtonColor: '#2657c1'
            });
        }

        if (errorEl) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorEl.dataset.message,
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#dc2626'
            });
        }

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Sesuai',
                text: 'Periksa kembali data akun yang kamu isi.',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#2657c1'
            });
        @endif
    });
</script>

@endsection