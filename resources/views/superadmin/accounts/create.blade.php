@extends('layouts.superadmin')

@section('title', 'Tambah Akun — SEROJAP')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <div class="relative overflow-hidden rounded-[28px] bg-white border border-slate-200 p-6 shadow-sm">

        <div class="absolute top-0 right-0 w-60 h-60 rounded-full bg-blue-50 -translate-y-1/2 translate-x-1/3"></div>

        <div class="relative z-10">
            <a href="{{ route('superadmin.accounts.index') }}"
               class="inline-flex items-center gap-2 text-[#2657c1] text-sm hover:underline font-bold">
                <i class="mdi mdi-arrow-left"></i>
                Kembali ke Manajemen Akun
            </a>

            <h1 class="text-3xl font-extrabold text-slate-900 mt-4">
                Tambah Akun
            </h1>

            <p class="text-slate-500 mt-2 leading-relaxed max-w-3xl">
                Super admin dapat membuat akun admin dan pelapor. Admin tidak bisa registrasi sendiri,
                sedangkan pelapor tetap bisa registrasi melalui halaman register.
            </p>
        </div>

    </div>

    @if(session('success'))
        <div id="swal-success" data-message="{{ session('success') }}" class="hidden"></div>
    @endif

    @if(session('error'))
        <div id="swal-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif

    <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/70 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center">
                <i class="mdi mdi-account-plus-outline text-2xl"></i>
            </div>

            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Form Tambah Akun
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Pilih jenis akun, isi data pengguna, lalu simpan akun baru.
                </p>
            </div>
        </div>

        <div class="p-6">

            <form action="{{ route('superadmin.accounts.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                        Jenis Akun
                    </label>

                    <select name="role"
                            class="w-full bg-slate-50 border @error('role') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                            required>
                        <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="pelapor" {{ old('role') === 'pelapor' ? 'selected' : '' }}>
                            Pelapor / User
                        </option>
                    </select>

                    <p class="text-xs text-slate-400 mt-2">
                        Super admin tidak dibuat dari form ini.
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
                           value="{{ old('nama') }}"
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
                           value="{{ old('email') }}"
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">
                            Password
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

                </div>

                <div class="pt-2 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">

                    <a href="{{ route('superadmin.accounts.index') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>

                    <button type="button"
                            onclick="confirmCreateAccount()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-sm font-extrabold bg-[#2657c1] hover:bg-[#1f4674] text-white transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        <i class="mdi mdi-content-save-outline"></i>
                        Tambah Akun
                    </button>

                </div>

                <input type="hidden" name="_confirm_submit" value="0">
                <button type="submit" id="confirmSubmitBtn" class="hidden"></button>

            </form>

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

<script>
    function confirmCreateAccount() {
        const role = document.querySelector('select[name="role"]').value;
        const nama = document.querySelector('input[name="nama"]').value;
        const email = document.querySelector('input[name="email"]').value;

        if (!role || !nama || !email) {
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Lengkap',
                text: 'Jenis akun, nama, dan email wajib diisi.',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#2657c1'
            });

            return;
        }

        const roleLabel = role === 'admin' ? 'Admin' : 'Pelapor / User';

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Tambah Akun',
            html: `Apakah data akun berikut sudah benar?<br><br>
                <b>Jenis Akun</b>: ${roleLabel}<br>
                <b>Nama</b>: ${nama}<br>
                <b>Email</b>: ${email}`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Tambah Akun',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-text-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-success',
                cancelButton: 'swal2-confirm-custom',
                backdrop: 'swal2-backdrop-custom',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="_confirm_submit"]').value = '1';
                document.getElementById('confirmSubmitBtn').click();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const successEl = document.getElementById('swal-success');
        const errorEl = document.getElementById('swal-error');

        if (successEl) {
            Swal.fire({
                icon: 'success',
                title: 'Akun Berhasil Ditambahkan',
                text: successEl.dataset.message,
                confirmButtonText: 'Oke',
                confirmButtonColor: '#2657c1'
            });
        }

        if (errorEl) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menambahkan Akun',
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