@extends('layouts.admin')

@section('title', 'Manajemen Akun Admin — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Akun Admin</h1>
        <p class="text-gray-500 mt-1">Tambah, edit, dan hapus akun admin (khusus super admin).</p>
    </div>

    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if(session('success') || session('error'))
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
                background-color: #4f46e5 !important;
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
            function confirmDelete(event, formEl) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Yakin ingin menghapus?',
                    text: 'Akun admin ini akan dihapus permanen.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    backdrop: true,
                    allowOutsideClick: true,
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
                        title: 'Berhasil Disimpan!',
                        text: successEl.dataset.message,
                        confirmButtonText: 'Oke, Tutup',
                        showConfirmButton: true,
                        timer: 4000,
                        timerProgressBar: true,
                        backdrop: true,
                        allowOutsideClick: true,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            icon: 'swal2-icon-custom',
                            title: 'swal2-title-custom',
                            htmlContainer: 'swal2-text-custom',
                            confirmButton: 'swal2-confirm-custom swal2-confirm-success',
                            backdrop: 'swal2-backdrop-custom',
                        }
                    });
                }

                if (errorEl) {
                    Swal.fire({
                        position: 'top-end',
                        toast: true,
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: errorEl.dataset.message,
                        confirmButtonText: 'Coba Lagi',
                        showConfirmButton: true,
                        timer: 5000,
                        timerProgressBar: true,
                        backdrop: true,
                        allowOutsideClick: true,
                        customClass: {
                            popup: 'swal2-popup-custom',
                            icon: 'swal2-icon-custom',
                            title: 'swal2-title-custom',
                            htmlContainer: 'swal2-text-custom',
                            confirmButton: 'swal2-confirm-custom swal2-confirm-error',
                            backdrop: 'swal2-backdrop-custom',
                        }
                    });
                }
            });
        </script>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">


        {{-- SISI KIRI: LIST ADMIN --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-sm font-semibold text-gray-700">
                    Daftar Admin <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">{{ $admins->count() }}</span>
                </h2>
            </div>

            <div class="p-5 max-h-[70vh] overflow-y-auto">
                @forelse($admins as $admin)
                    <div class="border border-gray-200 rounded-lg p-4 mb-3 bg-white hover:border-blue-300 transition-colors shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-gray-500">{{ $admin->posisi }}</div>
                                <div class="mt-1 font-bold text-gray-900 leading-tight">{{ $admin->name }}</div>
                                <div class="mt-1 text-sm text-gray-600 break-all">{{ $admin->email }}</div>
                            </div>

                            <div class="flex gap-2 flex-shrink-0">
                                <a href="{{ route('admin.admin-accounts.edit', $admin->id) }}"
                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                    Edit
                                </a>

                                @if($admin->role !== 'super_admin')
                                    <form action="{{ route('admin.admin-accounts.destroy', $admin->id) }}" method="POST" onsubmit="return confirmDelete(event, this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">📋</div>
                        <div class="font-semibold text-gray-900">Belum ada akun admin</div>
                        <div class="text-gray-500 text-sm mt-1">Gunakan form di sebelah kanan untuk menambah.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- SISI KANAN: FORM TAMBAH --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm sticky top-6">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    Tambah Admin
                </h2>
            </div>

            <div class="p-5">
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
                        <p id="email-error" class="text-red-500 text-sm mt-1" style="display:none;">Harus menggunakan email @gmail.com</p>
                        <p id="email-help" class="text-[11px] text-gray-500 mt-1">Contoh: nama@gmail.com</p>
                        @error('email')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password"
                            class="w-full bg-gray-50 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                        <p class="text-[11px] text-gray-500 mt-1">Password minimal 8 karakter</p>
                        @error('password')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full bg-gray-50 border @error('password_confirmation') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                            <p class="text-[11px] text-gray-500 mt-1">Password minimal 8 karakter</p>

                        @error('password_confirmation')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Posisi</label>
                        <input type="text" name="posisi" value="{{ old('posisi') }}"
                            class="w-full bg-gray-50 border @error('posisi') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                        <p class="text-[11px] text-gray-500 mt-1">Contoh: Admin Tim Operasional</p>
                        @error('posisi')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        Simpan Akun Admin
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

