@extends('layouts.superadmin')

@section('title', 'Manajemen Akun — SEROJAP')

@section('content')

@php
    $allAccounts = $users ?? collect();

    if ($allAccounts->isEmpty()) {
        $allAccounts = collect()
            ->merge($admins ?? collect())
            ->merge($pelapors ?? collect());
    }

    $totalAdmin = ($admins ?? $allAccounts->where('role', 'admin'))->count();
    $totalPelapor = ($pelapors ?? $allAccounts->where('role', 'pelapor'))->count();
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-200 p-6 shadow-sm">

        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-blue-50 -translate-y-1/2 translate-x-1/3"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-[#2657c1] text-xs font-bold mb-3">
                    <i class="mdi mdi-account-supervisor-circle-outline text-base"></i>
                    Kontrol Akun Sistem
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900">
                    Manajemen Akun
                </h1>

                <p class="text-slate-500 mt-2 max-w-3xl leading-relaxed">
                    Super admin dapat mengelola akun admin dan pelapor.
                    Pelapor tetap dapat registrasi sendiri melalui halaman publik.
                </p>
            </div>

            <a href="{{ route('superadmin.accounts.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#2657c1] text-white text-sm font-extrabold hover:bg-[#1f4674] transition shadow-md shadow-blue-500/20">
                <i class="mdi mdi-account-plus-outline text-lg"></i>
                Tambah Akun
            </a>

        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    @if(session('success'))
        <div id="swal-success" data-message="{{ session('success') }}" class="hidden"></div>
    @endif

    @if(session('error'))
        <div id="swal-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif

    <style>
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

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white border border-slate-200 rounded-[24px] p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Total Admin
                    </p>

                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                        {{ $totalAdmin }}
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center">
                    <i class="mdi mdi-account-tie-outline text-2xl"></i>
                </div>
            </div>

            <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                Admin hanya dibuat dan dikelola oleh super admin.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Total Pelapor
                    </p>

                    <h2 class="text-3xl font-extrabold text-slate-900 mt-2">
                        {{ $totalPelapor }}
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="mdi mdi-account-group-outline text-2xl"></i>
                </div>
            </div>

            <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                Pelapor bisa registrasi sendiri atau dikelola oleh super admin.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Hak Akses
                    </p>

                    <h2 class="text-xl font-extrabold text-[#2657c1] mt-2">
                        Super Admin
                    </h2>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="mdi mdi-shield-crown-outline text-2xl"></i>
                </div>
            </div>

            <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                Area khusus untuk kontrol akun sistem.
            </p>
        </div>

    </div>

    <!-- Account List -->
    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden shadow-sm">

        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/70 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Daftar Akun
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Menampilkan akun admin dan pelapor yang terdaftar pada sistem.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button"
                        onclick="filterAccount('all')"
                        class="account-filter-btn px-4 py-2 rounded-xl text-xs font-extrabold bg-[#2657c1] text-white transition"
                        data-filter="all">
                    Semua
                </button>

                <button type="button"
                        onclick="filterAccount('admin')"
                        class="account-filter-btn px-4 py-2 rounded-xl text-xs font-extrabold bg-white border border-slate-200 text-slate-600 hover:bg-blue-50 hover:text-blue-700 transition"
                        data-filter="admin">
                    Admin
                </button>

                <button type="button"
                        onclick="filterAccount('pelapor')"
                        class="account-filter-btn px-4 py-2 rounded-xl text-xs font-extrabold bg-white border border-slate-200 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition"
                        data-filter="pelapor">
                    Pelapor
                </button>
            </div>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4" id="accountGrid">

                @forelse($allAccounts as $account)

                    <div class="account-item border border-slate-200 rounded-[22px] p-5 bg-white hover:border-blue-300 hover:shadow-md transition"
                         data-role="{{ $account->role }}">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                            <div class="flex items-start gap-4 min-w-0">

                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#2657c1] to-[#226d71] text-white flex items-center justify-center font-bold text-sm flex-shrink-0 overflow-hidden shadow-sm">
                                    @if($account->foto_profil)
                                        <img src="{{ asset('storage/' . $account->foto_profil) }}"
                                             alt="Foto Profil"
                                             class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($account->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-900 leading-tight truncate">
                                        {{ $account->name }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500 break-all">
                                        {{ $account->email }}
                                    </div>

                                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                                        @if($account->role === 'admin')
                                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700">
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700">
                                                Pelapor
                                            </span>
                                        @endif

                                        <span class="text-[11px] text-slate-400">
                                            Dibuat: {{ $account->created_at?->format('d M Y') ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <div class="flex gap-2 flex-shrink-0 sm:justify-end">

                                <a href="{{ route('superadmin.accounts.edit', $account->id) }}"
                                   class="inline-flex items-center justify-center px-3 py-2 text-xs font-bold rounded-xl border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                    Edit
                                </a>

                                <form action="{{ route('superadmin.accounts.destroy', $account->id) }}"
                                      method="POST"
                                      onsubmit="return confirmDelete(event, this, '{{ $account->name }}', '{{ $account->role }}')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-bold rounded-xl border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-span-1 xl:col-span-2 text-center py-16">

                        <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 flex items-center justify-center text-3xl mb-4">
                            👥
                        </div>

                        <div class="font-extrabold text-slate-900">
                            Belum ada akun
                        </div>

                        <div class="text-slate-500 text-sm mt-1">
                            Gunakan tombol tambah akun untuk membuat data baru.
                        </div>

                    </div>

                @endforelse

            </div>

            <div id="accountEmptyFilter" class="hidden text-center py-16">
                <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-100 flex items-center justify-center text-3xl mb-4">
                    🔎
                </div>

                <div class="font-extrabold text-slate-900">
                    Tidak ada data pada filter ini
                </div>

                <div class="text-slate-500 text-sm mt-1">
                    Coba pilih filter lain.
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    function filterAccount(role) {
        const items = document.querySelectorAll('.account-item');
        const empty = document.getElementById('accountEmptyFilter');
        const buttons = document.querySelectorAll('.account-filter-btn');

        let visibleCount = 0;

        items.forEach(item => {
            const itemRole = item.dataset.role;

            if (role === 'all' || itemRole === role) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        buttons.forEach(button => {
            button.classList.remove('bg-[#2657c1]', 'text-white');
            button.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-600');

            if (button.dataset.filter === role) {
                button.classList.remove('bg-white', 'border', 'border-slate-200', 'text-slate-600');
                button.classList.add('bg-[#2657c1]', 'text-white');
            }
        });

        if (empty) {
            empty.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function confirmDelete(event, formEl, accountName, role) {
        event.preventDefault();

        const roleLabel = role === 'admin' ? 'admin' : 'pelapor';

        Swal.fire({
            icon: 'warning',
            title: 'Hapus Akun?',
            html: `Akun <b>${accountName}</b> dengan role <b>${roleLabel}</b> akan dihapus dari sistem.`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
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