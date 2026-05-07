@extends('layouts.admin')

@section('title', 'Riwayat Status — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Status</h1>
        <p class="text-gray-500 mt-1">Lihat semua riwayat perubahan status laporan</p>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.laporan.riwayat-status') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" placeholder="Cari kecamatan atau lokasi..."
                   value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5">
            <select name="status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2.5">
                <option value="">Semua Status</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="proses"   {{ request('status') == 'proses'   ? 'selected' : '' }}>Diproses</option>
                <option value="selesai"  {{ request('status') == 'selesai'  ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak"  {{ request('status') == 'ditolak'  ? 'selected' : '' }}>Ditolak</option>
            </select>
bg-[#1f4674] hover:bg-[#2657c1]
                Filter
            </button>
            <a href="{{ route('admin.laporan.riwayat-status') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                Reset
            </a>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Laporan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">#{{ $item->laporan->id_laporan ?? '-' }}</div>
                            <div class="text-gray-500 text-xs">{{ $item->laporan->kecamatan ?? '-' }} — {{ Str::limit($item->laporan->lokasi ?? '-', 40) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClasses = [
                                    'diterima' => 'bg-emerald-100 text-emerald-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                    'proses' => 'bg-amber-100 text-amber-700',
                                    'selesai' => 'bg-purple-100 text-purple-700',
                                ][$item->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 max-w-xs">
                            {{ $item->keterangan ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 text-xs">{{ $item->admin->name ?? 'Admin' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-gray-900 font-medium">Belum ada riwayat status</p>
                            <p class="text-gray-500 text-sm">Riwayat akan muncul setelah ada perubahan status</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($riwayat->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Menampilkan {{ $riwayat->firstItem() }}–{{ $riwayat->lastItem() }} dari {{ $riwayat->total() }} entri
            </span>
            <div class="flex gap-1">
                @if($riwayat->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg">‹ Prev</span>
                @else
                    <a href="{{ $riwayat->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">‹ Prev</a>
                @endif

                @foreach($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm rounded-lg border transition {{ $riwayat->currentPage() == $page ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($riwayat->hasMorePages())
                    <a href="{{ $riwayat->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next ›</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-400 border border-gray-200 rounded-lg">Next ›</span>
                @endif
            </div>
        @endif
    </div>
@endsection
