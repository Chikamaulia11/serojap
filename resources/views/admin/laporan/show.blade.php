@extends('layouts.admin')

@section('title', 'Detail Laporan #' . $laporan->id_laporan . ' — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Laporan
    </a>

    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Laporan</h1>
            <p class="text-gray-500 text-sm mt-1">ID #{{ $laporan->id_laporan }} · Dikirim {{ \Carbon\Carbon::parse($laporan->created_at)->diffForHumans() }}</p>
        </div>
@php $statusNow = $laporan->statusTerbaru?->status ?? 'baru'; @endphp
        @php
            $badgeClasses = [
                'baru' => 'bg-gray-100 text-gray-700',
                'diterima' => 'bg-emerald-100 text-emerald-700',
                'proses' => 'bg-amber-100 text-amber-700',
                'selesai' => 'bg-purple-100 text-purple-700',
                'ditolak' => 'bg-red-100 text-red-700',
            ][$statusNow] ?? 'bg-gray-100 text-gray-700';
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}">
            {{ ucfirst($statusNow) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KOLOM KIRI --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Pelapor --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Data Pelapor
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 text-white flex items-center justify-center font-bold text-lg">
                            {{ strtoupper(substr($laporan->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $laporan->user->name ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $laporan->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Laporan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Detail Laporan
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @if($laporan->foto)
                        <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto Kerusakan" class="w-full h-64 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-full h-48 bg-gray-50 border border-gray-200 rounded-lg flex flex-col items-center justify-center text-gray-400 gap-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-sm">Tidak ada foto</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kecamatan</p>
                            <p class="text-gray-900">{{ $laporan->kecamatan }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Lokasi / Koordinat</p>
                            <p class="text-gray-900">{{ $laporan->lokasi }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Deskripsi Kerusakan</p>
                        <p class="text-gray-900 leading-relaxed">{{ $laporan->deskripsi }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Laporan</p>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

            <!-- Riwayat Status -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Status
                    </h2>
                </div>
                <div class="px-6 py-4">
                    @if($laporan->statuses->count() > 0)
                        <div class="space-y-6">
@foreach($laporan->statuses()->orderBy('created_at', 'desc')->get() as $s)
                            <div class="flex gap-4 relative">
                                @if(!$loop->last)
                                    <div class="absolute left-4 top-8 bottom-[-24px] w-px bg-gray-200"></div>
                                @endif
                                @php
                                    $dotColors = [
                                        'diterima' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                        'ditolak' => 'bg-red-100 text-red-600 border-red-200',
                                        'proses' => 'bg-amber-100 text-amber-600 border-amber-200',
                                        'selesai' => 'bg-purple-100 text-purple-600 border-purple-200',
                                    ][$s->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                    $icons = ['diterima'=>'✓','ditolak'=>'✕','proses'=>'⚙','selesai'=>'★'];
                                @endphp
                                <div class="w-8 h-8 rounded-full border flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $dotColors }}">
                                    {{ $icons[$s->status] ?? '•' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-sm text-gray-900">{{ ucfirst($s->status) }}</span>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">oleh {{ $s->admin->name ?? 'Admin' }}</p>
                                    @if($s->keterangan)
                                        <div class="mt-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700">
                                            {{ $s->keterangan }}
                                        </div>
                                    @endif
                                    @if($s->foto_perbaikan)
                                        <img src="{{ asset('storage/' . $s->foto_perbaikan) }}" alt="Foto Perbaikan" class="mt-2 h-28 w-auto rounded-lg border border-gray-200 object-cover">
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-6">Belum ada update status untuk laporan ini.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Update -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm sticky top-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Update Status Laporan
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.laporan.update', $laporan->id_laporan) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Baru</label>
                            <select name="status" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="diterima">✓ Diterima</option>
                                <option value="proses">⚙ Diproses</option>
                                <option value="selesai">★ Selesai</option>
                                <option value="ditolak">✕ Ditolak</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" rows="4"
                                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 resize-y"
                                      placeholder="Contoh: Laporan telah diverifikasi dan akan ditindaklanjuti...">{{ old('keterangan') }}</textarea>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Foto Perbaikan (Opsional)</label>
                            <input type="file" name="foto_perbaikan" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maks 2MB. Upload saat status Selesai.</p>
                            @error('foto_perbaikan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm px-5 py-3 transition">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

