@extends('layouts.admin')

@section('title', 'Manajemen FAQ — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen FAQ</h1>
        <p class="text-gray-500 mt-1">Kelola pertanyaan dan jawaban untuk pelapor Serojap</p>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error Umum --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm flex items-center gap-2">
            <span>✕</span> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        {{-- SISI KIRI: DAFTAR FAQ --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    ❓ Daftar FAQ <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">{{ $faqs->count() }}</span>
                </h2>
            </div>

            <div class="p-5 max-h-[70vh] overflow-y-auto">
                @forelse($faqs as $faq)
                    <div class="border border-gray-200 rounded-lg p-4 mb-3 bg-white hover:border-blue-300 transition-colors shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-100 inline-flex px-2 py-0.5 rounded uppercase">
                                    Urutan: {{ $faq->urutan }}
                                </div>
                                <div class="mt-2 font-bold text-gray-900 leading-tight">{{ $faq->pertanyaan }}</div>
                                <div class="mt-2 text-sm text-gray-600 whitespace-pre-wrap">{{ Str::limit($faq->jawaban, 150) }}</div>
                            </div>

                            <div class="flex gap-2 flex-shrink-0">
                                {{-- Tombol Edit mengarah ke halaman edit terpisah --}}
                                <a href="{{ route('admin.manajemen-faq.edit', $faq->id_faq) }}" 
                                   class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                   Edit
                                </a>

                                <form action="{{ route('admin.manajemen-faq.destroy', $faq->id_faq) }}" method="POST" onsubmit="return confirm('Hapus FAQ ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-200 text-red-700 bg-red-50 hover:bg-red-100 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">📂</div>
                        <div class="font-semibold text-gray-900">Belum ada FAQ</div>
                        <div class="text-gray-500 text-sm mt-1">Gunakan form di sebelah kanan untuk menambah.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- SISI KANAN: FORM TAMBAH --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm sticky top-6">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    ➕ Tambah FAQ Baru
                </h2>
            </div>

            <div class="p-5">
                <form action="{{ route('admin.manajemen-faq.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pertanyaan</label>
                        <input type="text" name="pertanyaan" 
                               class="w-full bg-gray-50 border @error('pertanyaan') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" 
                               placeholder="Contoh: Bagaimana cara melaporkan jalan rusak?" 
                               value="{{ old('pertanyaan') }}" required>
                        @error('pertanyaan')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jawaban</label>
                        <textarea name="jawaban" 
                                  class="w-full bg-gray-50 border @error('jawaban') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm min-h-[150px] outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" 
                                  placeholder="Tuliskan jawaban lengkap di sini..." required>{{ old('jawaban') }}</textarea>
                        @error('jawaban')
                            <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Urutan Tampil</label>
                        <input type="number" name="urutan" 
                               class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500" 
                               placeholder="Kosongkan untuk otomatis" min="1" value="{{ old('urutan') }}">
                        <p class="text-[11px] text-gray-400 mt-2 italic">* Angka lebih kecil akan muncul paling atas di halaman pelapor.</p>
                    </div>

                    <button type="submit" class="w-full bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                        Simpan Data FAQ
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection