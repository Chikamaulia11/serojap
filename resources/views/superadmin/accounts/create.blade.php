@extends('layouts.superadmin')

@section('title', 'Tambah Akun — SEROJAP')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if(session('success'))
    <div id="swal-success" data-message="{{ session('success') }}" class="hidden"></div>
@endif

@if(session('error'))
    <div id="swal-error" data-message="{{ session('error') }}" class="hidden"></div>
@endif

<style>
    .create-page {
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

    .create-shell {
        background:
            linear-gradient(135deg, rgba(255,255,255,.98), rgba(244,249,255,.98)),
            radial-gradient(circle at right top, color-mix(in srgb, var(--accent) 13%, transparent), transparent 34%);
    }

    .info-card {
        background:
            linear-gradient(135deg, var(--band-from), var(--band-to));
    }

    .swal2-popup-custom {
        border-radius: 20px !important;
        padding: 2rem !important;
        font-family: inherit !important;
    }

    .swal2-title-custom {
        font-size: 1.4rem !important;
        font-weight: 700 !important;
        color: var(--ink) !important;
    }

    .swal2-text-custom {
        font-size: 0.95rem !important;
        color: var(--ink-soft) !important;
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
        background-color: var(--accent) !important;
        color: var(--on-accent) !important;
    }

    .swal2-confirm-error {
        background-color: var(--danger) !important;
        color: var(--on-danger) !important;
    }

    .swal2-backdrop-custom {
        backdrop-filter: blur(4px) !important;
        background: rgba(0, 0, 0, 0.35) !important;
    }
</style>

<div class="create-page max-w-6xl mx-auto space-y-5">

    <div>
        <a href="{{ route('superadmin.accounts.index') }}"
           class="inline-flex items-center gap-2 py-1 text-[var(--accent)] text-sm hover:underline font-extrabold">
            <i class="mdi mdi-arrow-left"></i>
            Kembali ke Manajemen Akun
        </a>
    </div>

    <section class="create-shell rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-200/70 relative overflow-hidden">

            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full bg-blue-100/40"></div>

            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-[var(--accent)] text-xs font-extrabold mb-4">
                    <i class="mdi mdi-account-plus-outline text-base"></i>
                    Form Tambah Akun
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                    Tambah Akun Baru
                </h1>

                <p class="text-sm md:text-base text-slate-500 mt-2 leading-relaxed">
                    Buat akun admin atau pelapor dengan data yang valid. Admin dibuat dari panel ini,
                    sedangkan pelapor tetap bisa registrasi mandiri melalui halaman publik.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">

            <!-- INFO -->
            <div class="lg:col-span-4 p-6 border-b lg:border-b-0 lg:border-r border-slate-200 bg-white/60">

                <div class="info-card rounded-3xl p-5 text-white relative overflow-hidden">

                    <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-white/10"></div>

                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center mb-4">
                            <i class="mdi mdi-shield-account-outline text-2xl"></i>
                        </div>

                        <h2 class="text-lg font-extrabold">
                            Catatan Akses
                        </h2>

                        <p class="text-sm text-white/85 mt-2 leading-relaxed">
                            Super admin tidak dibuat lewat form ini. Gunakan pilihan admin untuk petugas internal,
                            dan pelapor untuk akun masyarakat.
                        </p>

                        <div class="mt-5 space-y-3 text-sm">
                            <div class="flex items-start gap-2">
                                <i class="mdi mdi-check-circle-outline mt-0.5"></i>
                                <span>Admin tidak dapat registrasi sendiri.</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="mdi mdi-check-circle-outline mt-0.5"></i>
                                <span>Pelapor tetap bisa daftar mandiri.</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <i class="mdi mdi-check-circle-outline mt-0.5"></i>
                                <span>Password minimal 8 karakter.</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- FORM -->
            <div class="lg:col-span-8 p-6 bg-white/80">

                <form id="createAccountForm" action="{{ route('superadmin.accounts.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="md:col-span-2">
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Jenis Akun
                            </label>

                            <select name="role"
                                    class="w-full bg-white border @error('role') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                    required>
                                <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="pelapor" {{ old('role') === 'pelapor' ? 'selected' : '' }}>
                                    Pelapor / User
                                </option>
                            </select>

                            <p class="text-xs text-slate-500 mt-2">
                                Pilih jenis akun yang akan dibuat.
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
                                   value="{{ old('nama') }}"
                                   placeholder="Masukkan nama lengkap"
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
                                   value="{{ old('email') }}"
                                   placeholder="nama@gmail.com"
                                   class="w-full bg-white border @error('email') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            <p class="text-xs text-slate-500 mt-2">
                                Gunakan email aktif.
                            </p>

                            @error('email')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase tracking-[0.16em] mb-2">
                                Password
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
                                   placeholder="Ulangi password"
                                   class="w-full bg-white border @error('password_confirmation') border-red-500 @else border-slate-300 @enderror rounded-2xl px-4 py-3 text-sm outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition"
                                   required>

                            @error('password_confirmation')
                                <p class="text-red-600 text-xs mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-7 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">

                        <a href="{{ route('superadmin.accounts.index') }}"
                           class="inline-flex justify-center px-5 py-3 rounded-2xl text-sm font-extrabold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </a>

                        <button type="button"
                                onclick="confirmCreateAccount()"
                                class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-2xl text-sm font-extrabold bg-[var(--accent)] hover:bg-[var(--accent-deep)] text-white transition shadow-lg shadow-blue-500/20 active:scale-[0.98]">
                            <i class="mdi mdi-content-save-outline"></i>
                            Tambah Akun
                        </button>

                    </div>

                    <input type="hidden" name="_confirm_submit" value="0">
                    <button type="submit" id="confirmSubmitBtn" class="hidden"></button>

                </form>

            </div>

        </div>

    </section>

</div>

<script>
    function confirmCreateAccount() {
        const form = document.getElementById('createAccountForm');

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
                confirmButtonColor: 'var(--accent)'
            });
        }

        if (errorEl) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menambahkan Akun',
                text: errorEl.dataset.message,
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: 'var(--danger)'
            });
        }

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Data Belum Sesuai',
                text: 'Periksa kembali data akun yang kamu isi.',
                confirmButtonText: 'Oke',
                confirmButtonColor: 'var(--accent)'
            });
        @endif
    });
</script>

@endsection