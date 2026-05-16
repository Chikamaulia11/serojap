@extends('layouts.admin')

@section('title', 'Manajemen Akun Admin — SEROJAP')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.admin-accounts.index') }}" class="text-[#2657c1] text-sm hover:underline flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Manajemen Admin
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-3">Tambah Admin</h1>
        <p class="text-gray-500 text-sm">Super admin dapat menambahkan akun admin baru.</p>
    </div>

    @if(session('success'))
        <div id="swal-success" data-message="{{ session('success') }}" class="hidden"></div>
    @endif
    @if(session('error'))
        <div id="swal-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.admin-accounts.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                        class="w-full bg-gray-50 border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('nama')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('email')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full bg-gray-50 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('password')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-50 border @error('password_confirmation') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('password_confirmation')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Posisi</label>
                    <input type="text" name="posisi" value="{{ old('posisi') }}"
                        class="w-full bg-gray-50 border @error('posisi') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('posisi')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="button" onclick="confirmCreateAdmin()" class="w-full bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                    Tambah Admin
                </button>

                <input type="hidden" name="_confirm_submit" value="0">
                <button type="submit" id="confirmSubmitBtn" class="hidden"></button>

            </form>
        </div>
    </div>

</div>

{{-- SweetAlert2 CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<style>
    .swal2-popup-custom {
        border-radius: 20px !important;
        padding: 2rem !important;
        font-family: inherit !important;
    }
    .swal2-icon-custom {
        border: none !important;
        margin-bottom: 0.5rem !important;
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
    function confirmCreateAdmin() {
        if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
            alert('SweetAlert2 belum termuat. Coba refresh halaman.');
            return;
        }

        const nama  = document.querySelector('input[name="nama"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const posisi = document.querySelector('input[name="posisi"]').value;

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Tambah Admin',
            html: `Apakah data admin berikut sudah benar?<br><br>
                <b>Nama</b>: ${nama}<br>
                <b>Email</b>: ${email}<br>
                <b>Posisi</b>: ${posisi}`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Tambah Admin',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                popup:         'swal2-popup-custom',
                icon:          'swal2-icon-custom',
                title:         'swal2-title-custom',
                htmlContainer: 'swal2-text-custom',
                confirmButton: 'swal2-confirm-custom swal2-confirm-success',
                cancelButton:  'swal2-confirm-custom',
                backdrop:      'swal2-backdrop-custom',
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
        const errorEl   = document.getElementById('swal-error');

        if (successEl) {
            Swal.fire({
                icon: 'success',
                title: 'Admin Berhasil Ditambahkan!',
                text: successEl.dataset.message,
                confirmButtonText: 'Oke, Tutup',
                showConfirmButton: true,
                timer: 4000,
                timerProgressBar: true,
                backdrop: true,
                allowOutsideClick: true,
                customClass: {
                    popup:         'swal2-popup-custom',
                    icon:          'swal2-icon-custom',
                    title:         'swal2-title-custom',
                    htmlContainer: 'swal2-text-custom',
                    confirmButton: 'swal2-confirm-custom swal2-confirm-success',
                    backdrop:      'swal2-backdrop-custom',
                }
            });
        }

        if (errorEl) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menambahkan Admin',
                text: errorEl.dataset.message,
                confirmButtonText: 'Coba Lagi',
                showConfirmButton: true,
                timer: 5000,
                timerProgressBar: true,
                backdrop: true,
                allowOutsideClick: true,
                customClass: {
                    popup:         'swal2-popup-custom',
                    icon:          'swal2-icon-custom',
                    title:         'swal2-title-custom',
                    htmlContainer: 'swal2-text-custom',
                    confirmButton: 'swal2-confirm-custom swal2-confirm-error',
                    backdrop:      'swal2-backdrop-custom',
                }
            });
        }
    });
</script>

@endsection
