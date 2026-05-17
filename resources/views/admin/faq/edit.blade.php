@extends('layouts.admin')

@section('title', 'Edit FAQ — SEROJAP')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Breadcrumb / Navigasi Balik --}}
    <div class="mb-6">
        <a href="{{ route('admin.manajemen-faq.index') }}" class="text-[#2657c1] text-sm hover:underline flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Manajemen FAQ
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-3">Edit FAQ</h1>
        <p class="text-gray-500 text-sm">Perbarui informasi pertanyaan atau jawaban untuk pelapor.</p>
    </div>

    {{-- SweetAlert2 CDN (Disediakan jika layout utama belum memuatnya) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    {{-- Style Tambahan SweetAlert2 --}}
    <style>
        .swal2-popup-custom { border-radius: 20px !important; padding: 2rem !important; font-family: inherit !important; }
        .swal2-title-custom { font-size: 1.4rem !important; font-weight: 700 !important; color: #111827 !important; }
        .swal2-text-custom { font-size: 0.95rem !important; color: #6b7280 !important; }
        .swal2-confirm-custom { border-radius: 12px !important; padding: 0.6rem 2rem !important; font-weight: 600 !important; font-size: 0.95rem !important; box-shadow: none !important; border: none !important; }
        .swal2-confirm-success { background-color: #2657c1 !important; color: #fff !important; }
        .swal2-confirm-error { background-color: #dc2626 !important; color: #fff !important; }
        .swal2-backdrop-custom { backdrop-filter: blur(4px) !important; background: rgba(0, 0, 0, 0.35) !important; }
    </style>

    {{-- Script Validasi & Konfirmasi --}}
    <script>
        function confirmUpdateFaq(event, formEl) {
            event.preventDefault();

            const pertanyaan = formEl.querySelector('input[name="pertanyaan"]').value.trim();
            const jawaban    = formEl.querySelector('textarea[name="jawaban"]').value.trim();

            if (!pertanyaan || !jawaban) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Lengkap',
                    text: 'Pertanyaan dan jawaban wajib diisi sebelum memperbarui!',
                    confirmButtonText: 'Perbaiki',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        confirmButton: 'swal2-confirm-custom swal2-confirm-error'
                    }
                });
                return false;
            }

            Swal.fire({
                icon: 'question',
                title: 'Simpan Perubahan?',
                text: 'Pastikan data pertanyaan dan jawaban yang diubah sudah benar.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
                customClass: {
                    popup:         'swal2-popup-custom',
                    title:         'swal2-title-custom',
                    htmlContainer: 'swal2-text-custom',
                    confirmButton: 'swal2-confirm-custom swal2-confirm-success',
                    cancelButton:  'swal2-confirm-custom',
                    backdrop:      'swal2-backdrop-custom',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
            return false;
        }
    </script>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            {{-- Form Update --}}
            <form action="{{ route('admin.manajemen-faq.update', $faq->id_faq) }}" method="POST" onsubmit="return confirmUpdateFaq(event, this)">
                @csrf
                @method('PUT') 

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pertanyaan</label>
                    <input type="text" name="pertanyaan" 
                           class="w-full bg-gray-50 border @error('pertanyaan') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition" 
                           value="{{ old('pertanyaan', $faq->pertanyaan) }}">
                    @error('pertanyaan')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jawaban</label>
                    <textarea name="jawaban" 
                              class="w-full bg-gray-50 border @error('jawaban') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm min-h-[200px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition" 
                              required>{{ old('jawaban', $faq->jawaban) }}</textarea>
                    @error('jawaban')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Urutan Tampil</label>
                    <input type="number" name="urutan" 
                        class="w-full bg-gray-50 border @error('urutan') border-red-500 focus:ring-red-500/20 focus:border-red-500 @else border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @enderror rounded-lg px-3 py-2.5 text-sm outline-none transition" 
                        value="{{ old('urutan', $faq->urutan) }}">
                        @error('urutan')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror    
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.manajemen-faq.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection