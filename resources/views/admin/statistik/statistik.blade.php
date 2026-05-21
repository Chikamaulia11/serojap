@extends('layouts.admin') {{-- Ganti dengan nama file layout admin kamu, misal 'layouts.app' atau 'admin.layout' --}}

@section('content')
<div class="p-6 bg-[#f8f9fa] min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Grafik Statistik</h1>
            <p class="text-gray-500 text-sm">Visualisasi data laporan masuk tahun {{ date('Y') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">TOTAL</p>
                <h4 class="text-2xl font-black text-gray-800">{{ $statusCounts['total'] }}</h4>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-1">BARU</p>
                <h4 class="text-2xl font-black text-amber-600">{{ $statusCounts['baru'] }}</h4>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mb-1">PROSES</p>
                <h4 class="text-2xl font-black text-blue-600">{{ $statusCounts['proses'] }}</h4>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">SELESAI</p>
                <h4 class="text-2xl font-black text-emerald-600">{{ $statusCounts['selesai'] }}</h4>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">DITOLAK</p>
                <h4 class="text-2xl font-black text-rose-600">{{ $statusCounts['ditolak'] }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1.5 h-6 bg-[#2657c1] rounded-full"></div>
                    <h3 class="text-lg font-bold text-gray-800">Tren Pelaporan Bulanan</h3>
                </div>
                
                <div class="relative" style="height: 400px;">
                    <canvas id="chartSerojap"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartSerojap').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(38, 87, 193, 0.8)');
        gradient.addColorStop(1, 'rgba(38, 87, 193, 0.05)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Laporan',
                    data: @json($chartData),
                    backgroundColor: gradient,
                    borderColor: '#2657c1',
                    borderWidth: 2,
                    borderRadius: 8,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection