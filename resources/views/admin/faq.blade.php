@extends('layouts.admin') 

@section('title', 'Manajemen FAQ - Serojap')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Manajemen FAQ</h1>
    <p class="text-slate-500">Kelola pertanyaan dan jawaban FAQ untuk pengguna Serojap.</p>
</div>

<!-- Tabel Utama -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h2 class="font-semibold text-slate-700">Daftar FAQ</h2>
        <button onclick="openModal('tambah')" 
            class="bg-[#2657c1] hover:bg-[#1e44a3] text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <i class="mdi mdi-plus text-lg"></i> Tambah FAQ
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-6 py-4 border-b border-slate-100 w-20">Urutan</th>
                    <th class="px-6 py-4 border-b border-slate-100">Pertanyaan</th>
                    <th class="px-6 py-4 border-b border-slate-100">Jawaban</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($faqs as $faq)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-4 font-medium text-[#2657c1]">#{{ $faq->urutan }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-700">{{ $faq->pertanyaan }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ Str::limit($faq->jawaban, 80) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            <!-- Tombol Edit -->
                            <button onclick="openModal('edit', {{ json_encode($faq) }})" 
                                class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition">
                                <i class="mdi mdi-pencil-outline text-xl"></i>
                            </button>
                            
                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.manajemen-faq.destroy', $faq->id_faq) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <i class="mdi mdi-trash-can-outline text-xl"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">Belum ada data FAQ.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="modalFaq" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <div class="relative bg-white rounded-xl shadow-xl sm:max-w-lg sm:w-full overflow-hidden transition-all">
            <form id="faqForm" method="POST" class="p-6 text-left">
                @csrf
                <div id="methodField"></div> {{-- Tempat untuk @method('PUT') saat edit --}}
                
                <h3 id="modalTitle" class="text-lg font-bold text-slate-800 mb-4">Tambah FAQ</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pertanyaan</label>
                        <input type="text" name="pertanyaan" id="inputPertanyaan" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#2657c1] outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jawaban</label>
                        <textarea name="jawaban" id="inputJawaban" rows="4" required
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#2657c1] outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" id="inputUrutan" value="0"
                            class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-[#2657c1] outline-none">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#2657c1] text-white rounded-lg hover:bg-[#1e44a3]">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(mode, data = null) {
        const modal = document.getElementById('modalFaq');
        const form = document.getElementById('faqForm');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');

        modal.classList.remove('hidden');

        if (mode === 'edit') {
            title.innerText = 'Edit FAQ';
            form.action = `/admin/manajemen-faq/${data.id_faq}`;
            methodField.innerHTML = '@method("PUT")';
            document.getElementById('inputPertanyaan').value = data.pertanyaan;
            document.getElementById('inputJawaban').value = data.jawaban;
            document.getElementById('inputUrutan').value = data.urutan;
        } else {
            title.innerText = 'Tambah FAQ Baru';
            form.action = "{{ route('admin.manajemen-faq.store') }}";
            methodField.innerHTML = '';
            form.reset();
        }
    }

    function closeModal() {
        document.getElementById('modalFaq').classList.add('hidden');
    }
</script>
@endsection