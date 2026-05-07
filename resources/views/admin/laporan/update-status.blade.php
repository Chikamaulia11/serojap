@extends('layouts.admin')

@section('title', 'Update Status Laporan — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Update Status Laporan</h1>
        <p class="text-gray-500 mt-1">Perbarui status laporan jalan rusak secara cepat</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $laporan->total() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Baru</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ \App\Models\TabelLaporan::doesntHave('statuses')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Proses</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ \App\Models\TabelStatus::where('status','proses')->distinct('id_laporan')->count('id_laporan') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selesai</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ \App\Models\TabelStatus::where('status','selesai')->distinct('id_laporan')->count('id_laporan') }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ \App\Models\TabelStatus::where('status','ditolak')->distinct('id_laporan')->count('id_laporan') }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.laporan.update-status') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" placeholder="Cari kecamatan atau lokasi..."
                   value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5">
            <select name="status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5">
                <option value="">Semua Status</option>
                <option value="baru"     {{ request('status') == 'baru'     ? 'selected' : '' }}>Baru</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="proses"   {{ request('status') == 'proses'   ? 'selected' : '' }}>Diproses</option>
                <option value="selesai"  {{ request('status') == 'selesai'  ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak"  {{ request('status') == 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
            </select>
"bg-[#1f4674] hover:bg-[#2657c1] text-white font-medium rounded-lg text-sm px-5 py-2.5 transition"
                Filter
            </button>
            <a href="{{ route('admin.laporan.update-status') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Status Saat Ini</th>
                        <th class="px-6 py-4">Update Cepat</th>
                        <th class="px-6 py-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporan as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">#{{ $item->id_laporan }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $item->user->name ?? '-' }}</div>
                            <div class="text-gray-500 text-xs">{{ $item->user->email ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $item->kecamatan }}</div>
                            <div class="text-gray-500 text-xs truncate max-w-[180px]">{{ $item->lokasi }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $item->statusTerbaru?->status ?? 'baru';
                                $badgeClasses = [
                                    'baru' => 'bg-gray-100 text-gray-700',
                                    'diterima' => 'bg-emerald-100 text-emerald-700',
                                    'proses' => 'bg-amber-100 text-amber-700',
                                    'selesai' => 'bg-purple-100 text-purple-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                ][$status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.laporan.update', $item->id_laporan) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="status" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1.5">
                                    <option value="" disabled selected>-- Status --</option>
                                    <option value="diterima">✓ Diterima</option>
                                    <option value="proses">⚙ Diproses</option>
                                    <option value="selesai">★ Selesai</option>
                                    <option value="ditolak">✕ Ditolak</option>
                                </select>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg px-3 py-1.5 transition">
                                    Simpan
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.laporan.show', $item->id_laporan) }}"
                               class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <p class="text-gray-900 font-medium">Belum ada laporan</p>
                            <p class="text-gray-500 text-sm">Laporan yang masuk akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($laporan->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Menampilkan {{ $laporan->firstItem() }}–{{ $laporan->lastItem() }} dari {{ $laporan->total() }} laporan
            </span>
            <div class="flex gap-1">
                @if($laporan->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg">‹ Prev</span>
                @else
                    <a href="{{ $laporan->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">‹ Prev</a>
                @endif

                @foreach($laporan->getUrlRange(1, $laporan->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm rounded-lg border transition {{ $laporan->currentPage() == $page ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($laporan->hasMorePages())
                    <a href="{{ $laporan->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next ›</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg">Next ›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
