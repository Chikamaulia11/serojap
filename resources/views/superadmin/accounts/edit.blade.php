@extends('layouts.superadmin')

@section('title', 'Edit Akun — SEROJAP')

@section('content')

@php
    $target = $account ?? $admin ?? null;
@endphp

@if(!$target)
    <div class="max-w-4xl mx-auto">
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-3xl p-6">
            <h2 class="font-extrabold text-lg">
                Data akun tidak ditemukan
            </h2>

            <p class="text-sm mt-1">
                Akun yang ingin diedit tidak berhasil dikirim dari controller.
            </p>

            <a href="{{ route('superadmin.accounts.index') }}"
               class="inline-flex mt-4 px-5 py-2.5 rounded-2xl bg-red-600 text-white text-sm font-bold">
                Kembali ke Manajemen Akun
            </a>
        </div>
    </div>

    @php return; @endphp
@endif

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if($errors->any())
    <div id="swal-error-validation" class="hidden"></div>
@endif

<style>
    .edit-page {
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

    .edit-header {
        background:
            linear-gradient(135deg, rgba(255,255,255,.98), rgba(244,249,255,.98)),
            radial-gradient(circle at right top, rgba(38,87,193,.13), transparent 34%);
    }

    .password-note {
        background:
            linear-gradient(135deg, rgba(38,87,193,.08), rgba(34,109,113,.08));
    }

    .form-card {
        transition: .22s ease;
    }

    .form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(15,23,42,.06);
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

    .swal2-confirm-teal {
        background-color: #226d71 !important;
        color: #fff !important;
    }
</style>

<div class="edit-page max-w-6xl mx-auto space-y-5">

    <div>
        <a href="{{ route('superadmin.accounts.index') }}"
           class="inline-flex items-center gap-2 text-[#2657c1] text-sm hover:underline font-extrabold">
            <i class="mdi mdi-arrow-left"></i>
            Kembali ke Manajemen Akun
        </a>
    </div>

    <!-- HEADER -->
    <section class="edit-header rounded-3xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">

        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-blue-100/40"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

            <div class="flex items-center gap-4 min-w-0">

                <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-[#2657c1] to-[#226d71] flex items-center justify-center text-white font-extrabold text-xl overflow-hidden shadow-lg shadow-blue-100 flex-shrink-0">
                    @if($target->foto_profil)
                        <img src="{{ asset('storage/' . $target->foto_profil) }}"
                             alt="Foto Profil"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($target->name ?? 'A', 0, 1)) }}
                    @endif
                </div>

                <div class="min-w-0">

                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#2657c1] text-xs font-extrabold mb-2">
                        <i class="mdi mdi-account-edit-outline text-base"></i>
                        Edit Akun
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 truncate">
                        {{ $target->name ?? '-' }}
                    </h1>

                    <p class="text-sm text-slate-500 mt-1 break-all">
                        {{ $target->email ?? '-' }}
                    </p>

                </div>

            </div>

            <div>
                @if($target->role === 'admin')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700">
                        <i class="mdi mdi-account-tie-outline"></i>
                        Admin
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                        <i class="mdi mdi-account-outline"></i>
                        Pelapor
                    </span>
                @endif
            </div>

        </div>

    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- FORM PROFILE -->
        <div class="form-card bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-card-account-details-outline text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">
                            Edit Profil Akun
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Ubah nama, email, dan jenis akun pengguna.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">

                <form id="profileUpdateForm" action="{{ route('superadmin.accounts.update', $target->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="profile">

                    <div class="space-y-5">

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Jenis Akun
                            </label>

                            <select name="role"
                                    class="w-full bg-white border @error('role') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                    required>
                                <option value="admin" {{ old('role', $target->role) === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="pelapor" {{ old('role', $target->role) === 'pelapor' ? 'selected' : '' }}>
                                    Pelapor / User
                                </option>
                            </select>

                            <p class="text-xs text-slate-500 mt-2">
                                Super admin tidak diubah dari halaman ini.
                            </p>

                            @error('role')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Nama
                            </label>

                            <input type="text"
                                   name="nama"
                                   value="{{ old('nama', $target->name) }}"
                                   class="w-full bg-white border @error('nama') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            @error('nama')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Email
                            </label>

                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $target->email) }}"
                                   class="w-full bg-white border @error('email') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            <p class="text-xs text-slate-500 mt-2">
                                Gunakan email aktif. Contoh: nama@gmail.com
                            </p>

                            @error('email')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <button type="button"
                            onclick="confirmUpdateAccountProfil()"
                            class="mt-7 w-full inline-flex justify-center items-center gap-2 bg-[#2657c1] hover:bg-[#1f4674] text-white font-extrabold rounded-2xl px-4 py-3 transition shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                        <i class="mdi mdi-content-save-outline"></i>
                        Simpan Profil
                    </button>

                    <input type="hidden" name="_confirm_submit" value="0">
                    <button type="submit" id="confirmSubmitAccountProfileBtn" class="hidden"></button>

                </form>

            </div>

        </div>

        <!-- FORM PASSWORD -->
        <div class="form-card bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-[#226d71] flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-lock-reset text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">
                            Ubah Password
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Password baru digunakan saat login berikutnya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">

                <form id="passwordUpdateForm" action="{{ route('superadmin.accounts.update', $target->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="type" value="password">

                    <div class="password-note rounded-3xl border border-blue-100 p-5 mb-5">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white text-[#226d71] flex items-center justify-center flex-shrink-0">
                                <i class="mdi mdi-information-outline text-2xl"></i>
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed">
                                Pastikan password baru mudah diingat pengguna, tetapi tetap sulit ditebak.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5">

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Password Baru
                            </label>

                            <input type="password"
                                   name="password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full bg-white border @error('password') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            @error('password')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Konfirmasi Password
                            </label>

                            <input type="password"
                                   name="password_confirmation"
                                   placeholder="Ulangi password baru"
                                   class="w-full bg-white border @error('password_confirmation') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            @error('password_confirmation')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <button type="button"
                            onclick="confirmUpdateAccountPassword()"
                            class="mt-7 w-full inline-flex justify-center items-center gap-2 bg-[#226d71] hover:bg-[#1b575a] text-white font-extrabold rounded-2xl px-4 py-3 transition shadow-lg shadow-teal-500/20 active:scale-[0.98]">
                        <i class="mdi mdi-lock-check-outline"></i>
                        Simpan Password
                    </button>

                    <input type="hidden" name="_confirm_submit_password" value="0">
                    <button type="submit" id="confirmSubmitAccountPasswordBtn" class="hidden"></button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
    function confirmUpdateAccountProfil() {
        const form = document.getElementById('profileUpdateForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

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
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-text-custom',
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
        const form = document.getElementById('passwordUpdateForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Ganti Password Akun?',
            text: 'Password baru akan digunakan pengguna saat login berikutnya.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan Password',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            confirmButtonColor: '#226d71',
            cancelButtonColor: '#64748b',
            customClass: {
                popup: 'swal2-popup-custom',
                title: 'swal2-title-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-teal',
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