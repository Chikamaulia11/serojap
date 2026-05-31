@extends('layouts.superadmin')

@section('title', 'Edit Akun — SEROJAP')

@section('content')

@php
    $target = $account ?? $admin ?? null;
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-200 p-6 shadow-sm">

        <div class="absolute top-0 right-0 w-60 h-60 rounded-full bg-blue-50 -translate-y-1/2 translate-x-1/3"></div>

        <div class="relative z-10">
            <a href="{{ route('superadmin.accounts.index') }}"
               class="inline-flex items-center gap-2 text-[#2657c1] text-sm hover:underline font-bold">
                <i class="mdi mdi-arrow-left"></i>
                Kembali ke Manajemen Akun
            </a>

            <h1 class="text-3xl font-extrabold text-slate-900 mt-4">
                Edit Akun
            </h1>

            <p class="text-slate-500 mt-2 leading-relaxed max-w-3xl">
                Perbarui data akun atau ubah password pengguna yang terdaftar pada sistem SEROJAP.
            </p>
        </div>

    </div>

    @if($errors->any())
        <div id="swal-error-validation" class="hidden"></div>
    @endif

    <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-100 bg-slate-50/70 flex items-center gap-4">

            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#2657c1] to-[#226d71] flex items-center justify-center text-white font-extrabold overflow-hidden shadow-sm flex-shrink-0">
                @if($target?->foto_profil)
                    <img src="{{ asset('storage/' . $target->foto_profil) }}"
                         alt="Foto Profil"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($target->name ?? 'A', 0, 1)) }}
                @endif
            </div>

            <div class="min-w-0">
                <h2 class="font-extrabold text-slate-900 truncate">
                    {{ $target->name ?? '-' }}
                </h2>

                <p class="text-sm text-slate-500 break-all">
                    {{ $target->email ?? '-' }}
                </p>

                <div class="mt-2">
                    @if(($target->role ?? '') === 'admin')
                        <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700">
                            Admin
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700">
                            Pelapor
                        </span>
                    @endif
                </div>
            </div>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- FORM PROFILE -->
                <div class="rounded-[22px] border border-slate-200 p-5 bg-white">

                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center">
                            <i class="mdi mdi-account-edit-outline text-xl"></i>
                        </div>

                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">
                                Edit Profil Akun
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Ubah jenis akun, nama, dan email.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.accounts.update', $target->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="type" value="profile">

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                                Jenis Akun
                            </label>

                            <select name="role"
                                    class="w-full bg-slate-50 border @error('role') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    required>
                                <option value="admin" {{ old('role', $target->role) === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="pelapor" {{ old('role', $target->role) === 'pelapor' ? 'selected' : '' }}>
                                    Pelapor / User
                                </option>
                            </select>

                            <p class="text-xs text-slate-400 mt-2">
                                Jangan ubah akun menjadi super admin dari halaman ini.
                            </p>

                            @error('role')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                                Nama
                            </label>

                            <input type="text"
                                   name="nama"
                                   value="{{ old('nama', $target->name) }}"
                                   class="w-full bg-slate-50 border @error('nama') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                   required>

                            @error('nama')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                                Email
                            </label>

                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $target->email) }}"
                                   class="w-full bg-slate-50 border @error('email') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                   required>

                            <p class="text-xs text-slate-400 mt-2">
                                Gunakan email aktif. Contoh: nama@gmail.com
                            </p>

                            @error('email')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="button"
                                onclick="confirmUpdateAccountProfil()"
                                class="w-full inline-flex items-center justify-center gap-2 bg-[#2657c1] hover:bg-[#1f4674] text-white font-extrabold rounded-2xl px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                            <i class="mdi mdi-content-save-outline"></i>
                            Simpan Profil
                        </button>

                        <input type="hidden" name="_confirm_submit" value="0">
                        <button type="submit" id="confirmSubmitAccountProfileBtn" class="hidden"></button>

                    </form>

                </div>

                <!-- FORM PASSWORD -->
                <div class="rounded-[22px] border border-slate-200 p-5 bg-white">

                    <div class="flex items-start gap-3 mb-5">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="mdi mdi-lock-reset text-xl"></i>
                        </div>

                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">
                                Ubah Password
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Password baru akan dipakai pengguna saat login berikutnya.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.accounts.update', $target->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="type" value="password">

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                                Password Baru
                            </label>

                            <input type="password"
                                   name="password"
                                   class="w-full bg-slate-50 border @error('password') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                   required>

                            <p class="text-xs text-slate-400 mt-2">
                                Minimal 8 karakter.
                            </p>

                            @error('password')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                                Konfirmasi Password
                            </label>

                            <input type="password"
                                   name="password_confirmation"
                                   class="w-full bg-slate-50 border @error('password_confirmation') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                   required>

                            <p class="text-xs text-slate-400 mt-2">
                                Ulangi password yang sama.
                            </p>

                            @error('password_confirmation')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="button"
                                onclick="confirmUpdateAccountPassword()"
                                class="w-full inline-flex items-center justify-center gap-2 bg-[#2657c1] hover:bg-[#1f4674] text-white font-extrabold rounded-2xl px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                            <i class="mdi mdi-content-save-outline"></i>
                            Simpan Password
                        </button>

                        <input type="hidden" name="_confirm_submit_password" value="0">
                        <button type="submit" id="confirmSubmitAccountPasswordBtn" class="hidden"></button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<style>
    .swal2-popup-custom {
        border-radius: 20px !important;
        padding: 2rem !important;
        font-family: inherit !important;
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
</style>

<script>
    function confirmUpdateAccountProfil() {
        const role = document.querySelector('select[name="role"]').value;
        const nama = document.querySelector('input[name="nama"]').value;
        const email = document.querySelector('input[name="email"]').value;

        const roleLabel = role === 'admin' ? 'Admin' : 'Pelapor / User';

        Swal.fire({
            icon: 'warning',
            title: 'Simpan Perubahan Profil?',
            html: `Pastikan data akun berikut sudah benar:<br><br>
                <b>Jenis Akun</b>: ${roleLabel}<br>
                <b>Nama</b>: ${nama}<br>
                <b>Email</b>: ${email}`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#2657c1',
            cancelButtonColor: '#64748b',
            customClass: {
                popup: 'swal2-popup-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-success',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="_confirm_submit"]').value = '1';
                document.getElementById('confirmSubmitAccountProfileBtn').click();
            }
        });
    }

    function confirmUpdateAccountPassword() {
        Swal.fire({
            icon: 'warning',
            title: 'Ganti Password Akun?',
            text: 'Password baru akan digunakan pengguna saat login berikutnya.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan Password',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#2657c1',
            cancelButtonColor: '#64748b',
            customClass: {
                popup: 'swal2-popup-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-success',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="_confirm_submit_password"]').value = '1';
                document.getElementById('confirmSubmitAccountPasswordBtn').click();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
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