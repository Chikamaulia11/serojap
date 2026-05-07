@php
    // $laporanSaatIni optional; $daftarLaporan wajib
@endphp

@if(isset($daftarLaporan) && $daftarLaporan->count())
<div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Ganti Laporan
        </h2>
    </div>

    <div class="px-6 py-4">
        <form method="GET" action="{{ route('admin.laporan.show', ['id' => $laporanSaatIni->id_laporan ?? 0]) }}" onsubmit="">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Laporan</label>
            <select name="id" onchange="this.form.submit();"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                @foreach($daftarLaporan as $l)
                    <option value="{{ $l->id_laporan }}" {{ (isset($laporanSaatIni) && $laporanSaatIni->id_laporan == $l->id_laporan) ? 'selected' : '' }}>
                        #{{ $l->id_laporan }} — {{ $l->kecamatan }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif