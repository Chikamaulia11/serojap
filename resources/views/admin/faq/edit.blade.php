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

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            {{-- Form Update --}}
            <form action="{{ route('admin.manajemen-faq.update', $faq->id_faq) }}" method="POST">
                @csrf
                @method('PUT') 

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pertanyaan</label>
                    <input type="text" name="pertanyaan" 
                            class="w-full bg-gray-50 border @error('pertanyaan') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition" 
                            value="{{ old('pertanyaan', $faq->pertanyaan) }}" required>
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
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-blue-500 outline-none" 
                            value="{{ old('urutan', $faq->urutan) }}">
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="confirmUpdateFaq()" class="flex-1 bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        Simpan Perubahan
                    </button>

                    <input type="hidden" name="_confirm_submit" value="0">
                    <button type="submit" id="confirmSubmitFaqBtn" class="hidden"></button>
                    <a href="{{ route('admin.manajemen-faq.index') }}" 
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- SweetAlert2 CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    function confirmUpdateFaq() {
        if (typeof Swal === 'undefined' || typeof Swal.fire !== 'function') {
            alert('SweetAlert2 belum termuat. Coba refresh halaman.');
            return;
        }

        const pertanyaan = document.querySelector('input[name="pertanyaan"]').value;
        const jawaban = document.querySelector('textarea[name="jawaban"]').value;

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi update FAQ',
            html: `Apakah data FAQ berikut benar?<br><br>
                <b>Pertanyaan</b>: ${pertanyaan}<br>
                <b>Jawaban</b>: ${jawaban}`,
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                popup: 'swal2-popup-custom',
                confirmButton: 'swal2-confirm-custom',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('input[name="_confirm_submit"]').value = '1';
                document.getElementById('confirmSubmitFaqBtn').click();
            }
        });
    }
</script>

@endsection
