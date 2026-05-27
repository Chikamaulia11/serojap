@extends('layouts.admin')

@section('title', 'Statistik Laporan — SEROJAP')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Statistik Laporan</h1>
        <p class="text-gray-500 mt-1">Ringkasan dan analisis data laporan jalan rusak tahun {{ now()->year }}</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $total }}</p>
            <p class="text-xs text-gray-500 mt-2">Semua laporan</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Baru</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $baru }}</p>
            <p class="text-xs text-gray-500 mt-2">Belum diproses</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Diterima</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $diterima }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $total > 0 ? round($diterima/$total*100) : 0 }}%</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Proses</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $proses }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $total > 0 ? round($proses/$total*100) : 0 }}%</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selesai</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $selesai }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $total > 0 ? round($selesai/$total*100) : 0 }}%</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $ditolak }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $total > 0 ? round($ditolak/$total*100) : 0 }}%</p>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Donut Chart --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-2">
                <h2 class="text-sm font-semibold text-gray-700">Distribusi Status Laporan</h2>
                <p class="text-xs text-gray-500">Persentase berdasarkan status terkini</p>
            </div>

            <div class="flex items-center gap-6">
                <div class="w-56 max-w-[230px]">
                    <canvas id="donutChart" height="220"></canvas>
                </div>

                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span>
                        <span class="text-sm text-gray-600">Baru</span>
                        <span class="ml-auto font-semibold text-sm text-gray-900">{{ $baru }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-sm text-gray-600">Diterima</span>
                        <span class="ml-auto font-semibold text-sm text-gray-900">{{ $diterima }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-sm text-gray-600">Diproses</span>
                        <span class="ml-auto font-semibold text-sm text-gray-900">{{ $proses }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                        <span class="text-sm text-gray-600">Selesai</span>
                        <span class="ml-auto font-semibold text-sm text-gray-900">{{ $selesai }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span class="text-sm text-gray-600">Ditolak</span>
                        <span class="ml-auto font-semibold text-sm text-gray-900">{{ $ditolak }}</span>
                    </div>
                </div>
            </div>
        </div>


        {{-- Bar Chart Bulanan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-2">
                <h2 class="text-sm font-semibold text-gray-700">Laporan Masuk per Bulan</h2>
                <p class="text-xs text-gray-500">Data tahun {{ now()->year }}</p>
            </div>
            <canvas id="barChart" height="160"></canvas>
        </div>
    </div>

    <!-- {{-- Top Kecamatan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h2 class="text-sm font-semibold text-gray-700">🏭 Top Kecamatan</h2>
            <p class="text-xs text-gray-500">5 kecamatan dengan laporan terbanyak</p>
        </div>

        @php($maxKec = $perKecamatan->max('jumlah') ?: 1)

        @if($perKecamatan->count() > 0)
            <div class="space-y-3">
                @foreach($perKecamatan as $i => $kec)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $i === 0 ? 'bg-blue-50 text-[#2657c1] border border-blue-100' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                            #{{ $i+1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900">{{ $kec->kecamatan ?: 'Tidak diketahui' }}</div>
                            <div class="mt-1">
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#2657c1]" style="width:{{ round($kec->jumlah/$maxKec*100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-sm font-semibold text-gray-900 w-16 text-right">{{ $kec->jumlah }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 text-sm text-center py-8">Belum ada data</div>
        @endif
    </div> -->

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const donutData = {
            labels: ['Baru', 'Diterima', 'Diproses', 'Selesai', 'Ditolak'],
            datasets: [{
                data: [{{ $baru }}, {{ $diterima }}, {{ $proses }}, {{ $selesai }}, {{ $ditolak }}],
                backgroundColor: ['#64748b', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        };

        const barData = {
            labels: {!! json_encode($labelBulan) !!},
            datasets: [{
                label: 'Laporan',
                data: {!! json_encode($dataBulan) !!},
                backgroundColor: 'rgba(59,130,246,0.5)',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 6,
            }]
        };

        Chart.defaults.color = '#64748b';
        Chart.defaults.borderColor = '#e5e7eb';

        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: donutData,
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                },
                animation: {
                    animateScale: true
                }
            }
        });


        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: '#e5e7eb' } },
                    y: { grid: { color: '#e5e7eb' }, beginAtZero: true }
                }
            }
        });
    </script>
</div>
@endsection