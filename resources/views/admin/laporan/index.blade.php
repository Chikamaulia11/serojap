@extends('layouts.admin')

@section('title', 'Manajemen Laporan — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Manajemen Laporan
        </h1>

        <p class="text-gray-500 mt-1">
            Kelola dan verifikasi semua laporan jalan rusak yang masuk
        </p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Total
            </p>

            <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ $laporan->total() }}
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Diterima
            </p>

            <p class="text-2xl font-bold text-emerald-600 mt-1">
                {{ \App\Models\TabelStatus::where('status','diterima')->distinct('report_id')->count('report_id') }}
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Diproses
            </p>

            <p class="text-2xl font-bold text-blue-600 mt-1">
                {{ \App\Models\TabelStatus::where('status','diproses')->distinct('report_id')->count('report_id') }}
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Selesai
            </p>

            <p class="text-2xl font-bold text-emerald-600 mt-1">
                {{ \App\Models\TabelStatus::where('status','selesai')->distinct('report_id')->count('report_id') }}
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Ditolak
            </p>

            <p class="text-2xl font-bold text-red-600 mt-1">
                {{ \App\Models\TabelStatus::where('status','ditolak')->distinct('report_id')->count('report_id') }}
            </p>
        </div>

    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">

        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
            class="flex flex-wrap gap-3"
        >

            <input
                type="text"
                name="search"
                placeholder="Cari alamat atau keterangan..."
                value="{{ request('search') }}"
                class="flex-1 min-w-[200px] bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg px-4 py-2.5"
            >

            <select
                name="status"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg px-4 py-2.5 pr-10"
            >

                <option value="">Semua Status</option>

                <option value="diterima"
                    {{ request('status') == 'diterima' ? 'selected' : '' }}>
                    Diterima
                </option>

                <option value="diproses"
                    {{ request('status') == 'diproses' ? 'selected' : '' }}>
                    Diproses
                </option>

                <option value="selesai"
                    {{ request('status') == 'selesai' ? 'selected' : '' }}>
                    Selesai
                </option>

                <option value="ditolak"
                    {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                    Ditolak
                </option>

            </select>

            <button
                type="submit"
                class="bg-[#1f4674] hover:bg-[#2657c1] text-white font-medium rounded-lg text-sm px-5 py-2.5 transition"
            >
                Filter
            </button>

            <a
                href="{{ route('admin.laporan.index') }}"
                class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 transition"
            >
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
                        <th class="px-6 py-4">Foto</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                    @php
                        $showLimit = 5;
                    @endphp

                    @forelse($laporan as $idx => $item)

                        @php
                            $isHidden = ($idx >= $showLimit);
                        @endphp

                    @php


                        $status = $item->statusTerbaru?->status ?? 'diterima';

                        $badgeClasses = [

                            'diterima' => 'bg-emerald-100 text-emerald-700',
                            'diproses' => 'bg-amber-100 text-amber-700',
                            'selesai'  => 'bg-purple-100 text-purple-700',
                            'ditolak'  => 'bg-red-100 text-red-700',

                        ][$status] ?? 'bg-gray-100 text-gray-700';

                    @endphp

                    <tr class="hover:bg-gray-50 transition {{ $isHidden ? 'hidden' : '' }}" data-row>


                        <td class="px-6 py-4">

                            @if($item->foto)

                                <img
                                    src="{{ asset('storage/' . $item->foto) }}"
                                    alt="foto"
                                    class="w-12 h-12 rounded-lg object-cover border border-gray-200"
                                >

                            @else

                                <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                    -
                                </div>

                            @endif

                        </td>

                        <td class="px-6 py-4">

                            <div class="font-medium text-gray-900">
                                {{ $item->user->name ?? $item->nama_pelapor }}
                            </div>

                            <div class="text-gray-500 text-xs">
                                {{ $item->user->email ?? '-' }}
                            </div>

                        </td>

                        <td class="px-6 py-4">

                            <div class="font-medium text-gray-900">
                                {{ $item->alamat }}
                            </div>

                            <div class="text-gray-500 text-xs truncate max-w-[180px]">
                                {{ $item->latitude }}, {{ $item->longitude }}
                            </div>

                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            {{ $item->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                {{ ucfirst($status) }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-right">

                            <a
                                href="{{ route('admin.laporan.show', $item->id) }}"
                                class="inline-flex items-center gap-1 bg-primary-50 hover:bg-primary-500 text-primary-700 hover:text-white text-xs font-medium px-3 py-1.5 rounded-lg transition"
                            >
                                Detail
                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6" class="px-6 py-12 text-center">

                            <p class="text-gray-900 font-medium">
                                Belum ada laporan
                            </p>

                            <p class="text-gray-500 text-sm">
                                Laporan yang masuk akan muncul di sini
                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">

    {{-- Progress + count label --}}
    <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400" id="countLabel">
            Menampilkan {{ min($showLimit, $laporan->count()) }} dari {{ $laporan->count() }} laporan
        </span>
        <div class="w-20 h-1 bg-gray-200 rounded-full overflow-hidden">
            <div
                id="progressBar"
                class="h-full bg-blue-500 rounded-full transition-all duration-400"
                style="width: {{ $laporan->count() > 0 ? round((min($showLimit, $laporan->count()) / $laporan->count()) * 100) : 0 }}%"
            ></div>
        </div>
    </div>

    {{-- View more button --}}
            <button
                type="button"
                id="viewMoreBtn"
                class="hidden inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition">
                <svg id="btnChevron" class="w-3.5 h-3.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                <span id="btnLabel">Tampilkan lainnya</span>
                <span id="remainCount" class="text-gray-400"></span>
            </button>

        </div>

        </div>

        {{-- Pagination --}}
        @if($laporan->hasPages())

        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">

            <span class="text-sm text-gray-500">
                Menampilkan
                {{ $laporan->firstItem() }}
                –
                {{ $laporan->lastItem() }}
                dari
                {{ $laporan->total() }}
                laporan
            </span>

            <div>
                {{ $laporan->links() }}
            </div>

        </div>

        @endif

    </div>

</div>

<script>
(function () {
    const btn       = document.getElementById('viewMoreBtn');
    const bar       = document.getElementById('progressBar');
    const label     = document.getElementById('countLabel');
    const remain    = document.getElementById('remainCount');
    const chevron   = document.getElementById('btnChevron');
    if (!btn) return;

    const hidden = [...document.querySelectorAll('tr[data-row].hidden')];
    const total  = document.querySelectorAll('tr[data-row]').length;
    let shown    = total - hidden.length;

    if (hidden.length === 0) return;

    remain.textContent = '(+' + hidden.length + ')';
    btn.classList.remove('hidden');

    btn.addEventListener('click', function () {
        btn.disabled = true;
        chevron.style.transform = 'rotate(180deg)';

        let i = 0;
        const tick = setInterval(function () {
            if (i >= hidden.length) {
                clearInterval(tick);
                bar.style.width = '100%';
                label.textContent = 'Menampilkan ' + total + ' dari ' + total + ' laporan';
                btn.remove();
                return;
            }
            const row = hidden[i];
            row.classList.remove('hidden');
            row.style.opacity = '0';
            row.style.transform = 'translateY(6px)';
            row.style.transition = 'opacity 250ms ease, transform 250ms ease';
            requestAnimationFrame(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            });
            shown++;
            i++;
            label.textContent = 'Menampilkan ' + shown + ' dari ' + total + ' laporan';
            bar.style.width = Math.round((shown / total) * 100) + '%';
            }, 120);
        });
    })();
</script>
@endsection
