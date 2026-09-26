@extends('layouts.admin')

@section('title', 'Statistik Laporan — SEROJAP')

@section('content')

<style>
    .stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        transition: 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
    }

    .chart-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .chart-header {
        margin-bottom: 22px;
    }

    .chart-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .chart-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .donut-layout {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 28px;
        align-items: center;
    }

    .donut-box {
        width: 240px;
        height: 240px;
        position: relative;
    }

    .donut-box canvas {
        width: 240px !important;
        height: 240px !important;
    }

    .legend-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-label {
        font-size: 14px;
        color: #475569;
        font-weight: 600;
    }

    .legend-value {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .bar-box {
        height: 300px;
        position: relative;
    }

    .bar-box canvas {
        width: 100% !important;
        height: 300px !important;
    }

    @media (max-width: 1100px) {
        .donut-layout {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .legend-grid {
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .donut-box,
        .donut-box canvas {
            width: 210px !important;
            height: 210px !important;
        }

        .chart-card {
            padding: 18px;
        }

        .bar-box,
        .bar-box canvas {
            height: 260px !important;
        }
    }
</style>

<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            Statistik Laporan
        </h1>

        <p class="text-gray-500 mt-1">
            Ringkasan dan analisis data laporan jalan rusak tahun {{ now()->year }}
        </p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Total
            </p>

            <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ $total }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                Semua laporan
            </p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Baru
            </p>

            <p class="text-2xl font-bold text-gray-900 mt-1">
                {{ $baru }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                Belum diproses
            </p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Diterima
            </p>

            <p class="text-2xl font-bold text-emerald-600 mt-1">
                {{ $diterima }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                {{ $total > 0 ? round($diterima / $total * 100) : 0 }}%
            </p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Diproses
            </p>

            <p class="text-2xl font-bold text-blue-600 mt-1">
                {{ $proses }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                {{ $total > 0 ? round($proses / $total * 100) : 0 }}%
            </p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Selesai
            </p>

            <p class="text-2xl font-bold text-purple-600 mt-1">
                {{ $selesai }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                {{ $total > 0 ? round($selesai / $total * 100) : 0 }}%
            </p>
        </div>

        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                Ditolak
            </p>

            <p class="text-2xl font-bold text-red-600 mt-1">
                {{ $ditolak }}
            </p>

            <p class="text-xs text-gray-500 mt-2">
                {{ $total > 0 ? round($ditolak / $total * 100) : 0 }}%
            </p>
        </div>

    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Donut Chart --}}
        <div class="chart-card">

            <div class="chart-header">
                <h2 class="chart-title">
                    Distribusi Status Laporan
                </h2>

                <p class="chart-subtitle">
                    Persentase berdasarkan status terkini
                </p>
            </div>

            <div class="donut-layout">

                <div class="donut-box">
                    <canvas id="donutChart"></canvas>
                </div>

                <div class="legend-grid">

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:#64748b;"></span>
                            <span class="legend-label">Baru</span>
                        </div>

                        <span class="legend-value">{{ $baru }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:#10b981;"></span>
                            <span class="legend-label">Diterima</span>
                        </div>

                        <span class="legend-value">{{ $diterima }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:#f59e0b;"></span>
                            <span class="legend-label">Diproses</span>
                        </div>

                        <span class="legend-value">{{ $proses }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:#8b5cf6;"></span>
                            <span class="legend-label">Selesai</span>
                        </div>

                        <span class="legend-value">{{ $selesai }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:#ef4444;"></span>
                            <span class="legend-label">Ditolak</span>
                        </div>

                        <span class="legend-value">{{ $ditolak }}</span>
                    </div>

                </div>

            </div>

        </div>

        {{-- Bar Chart Bulanan --}}
        <div class="chart-card">

            <div class="chart-header">
                <h2 class="chart-title">
                    Laporan Masuk per Bulan
                </h2>

                <p class="chart-subtitle">
                    Data tahun {{ now()->year }}
                </p>
            </div>

            <div class="bar-box">
                <canvas id="barChart"></canvas>
            </div>

        </div>

    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const donutData = {
        labels: ['Baru', 'Diterima', 'Diproses', 'Selesai', 'Ditolak'],
        datasets: [{
            data: [
                {{ $baru }},
                {{ $diterima }},
                {{ $proses }},
                {{ $selesai }},
                {{ $ditolak }}
            ],
            backgroundColor: [
                '#64748b',
                '#10b981',
                '#f59e0b',
                '#8b5cf6',
                '#ef4444'
            ],
            borderWidth: 4,
            borderColor: '#ffffff',
            hoverOffset: 8
        }]
    };

    const barData = {
        labels: {!! json_encode($labelBulan) !!},
        datasets: [{
            label: 'Laporan',
            data: {!! json_encode($dataBulan) !!},
            backgroundColor: 'rgba(59,130,246,0.45)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            borderRadius: 8,
            barThickness: 28,
            maxBarThickness: 34
        }]
    };

    Chart.defaults.font.family = "'Inter', 'Public Sans', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e5e7eb';

    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: donutData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 10
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: barData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 10
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1
                    },
                    grid: {
                        color: '#edf2f7'
                    }
                }
            }
        }
    });
</script>

@endsection