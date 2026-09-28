@extends('layouts.admin')

@section('title', 'Statistik Laporan — SEROJAP')

@section('content')

<style>
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--line);
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
        background: var(--surface);
        border: 1px solid var(--line);
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
        color: var(--ink);
        margin: 0;
    }

    .chart-subtitle {
        font-size: 13px;
        color: var(--ink-soft);
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
        background: var(--bg);
        border: 1px solid var(--accent-tint);
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
        color: var(--ink-soft);
        font-weight: 600;
    }

    .legend-value {
        font-size: 15px;
        font-weight: 800;
        color: var(--ink);
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
                            <span class="legend-dot" style="background:var(--ink-soft);"></span>
                            <span class="legend-label">Baru</span>
                        </div>

                        <span class="legend-value">{{ $baru }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:var(--selesai);"></span>
                            <span class="legend-label">Diterima</span>
                        </div>

                        <span class="legend-value">{{ $diterima }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:var(--diproses);"></span>
                            <span class="legend-label">Diproses</span>
                        </div>

                        <span class="legend-value">{{ $proses }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:var(--accent);"></span>
                            <span class="legend-label">Selesai</span>
                        </div>

                        <span class="legend-value">{{ $selesai }}</span>
                    </div>

                    <div class="legend-item">
                        <div class="legend-left">
                            <span class="legend-dot" style="background:var(--danger);"></span>
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
    /* Chart.js melukis ke <canvas>, dan `fillStyle` di sana TIDAK membaca
       CSS variable -- `var(--x)` akan diabaikan diam-diam dan grafiknya
       jadi hitam/putih polos. Jadi warnanya harus diresolvasikan ke
       warna nyata lewat `SerojapTheme.themeColor()` (lihat
       `resources/js/theme.js`), yang memakai elemen probe supaya
       `var()` dan `color-mix()` ikut selesai oleh browser.

       Konsekuensi kedua: warna hanya dibaca SEKALI saat chart dibuat,
       jadi begitu pengguna mengganti aksen atau light/dark, chart lama
       masih memakai warna lamanya. Karena itu `drawCharts()` dipanggil
       ulang lewat `onThemeChange()`. */
    const themeColor = (n) => window.SerojapTheme.themeColor(n);

    /* Versi transparan dari sebuah token, untuk Mewantikan `rgba()` lama.
       `themeColor()` sudah mengembalikan bentuk `rgb(r, g, b)`, jadi
       angkanya tinggal diambil ulang. */
    const themeAlpha = (n, alpha) => {
        const parts = themeColor(n).match(/[\d.]+/g);
        return parts ? `rgba(${parts[0]}, ${parts[1]}, ${parts[2]}, ${alpha})` : themeColor(n);
    };

    const $baru = {{ $baru }};
    const $diterima = {{ $diterima }};
    const $proses = {{ $proses }};
    const $selesai = {{ $selesai }};
    const $ditolak = {{ $ditolak }};

    let donut = null;
    let bar = null;

    function drawCharts() {
        /* `drawCharts()` dipanggil ulang setiap kali tema berubah, dan
           `new Chart()` pada canvas yang sama akan menumpuk instance lama
           di atas yang baru tanpa melepaskannya. Akibatnya listener dan
           animasi menumpuk, dan setelah beberapa kali pergantian tema
           grafiknya makin berat. Jadi instance sebelumnya dibongkar
           dulu setiap kali dibangun ulang. */
        [donut, bar].forEach((c) => {
            if (c) c.destroy();
        });
        donut = null;
        bar = null;

        const tooltip = () => ({
            backgroundColor: themeColor('--ink'),
            titleColor: themeColor('--on-ink'),
            bodyColor: themeColor('--on-ink'),
            padding: 12,
            cornerRadius: 10
        });

        /* Warna di sini persis mengikuti arti labelnya, bukan warna
           aksen: ganti tema tidak boleh mengubah makna warna status. */
        const donutData = {
            labels: ['Baru', 'Diterima', 'Diproses', 'Selesai', 'Ditolak'],
            datasets: [{
                data: [$baru, $diterima, $proses, $selesai, $ditolak],
                backgroundColor: [
                    themeColor('--ink-soft'),
                    themeColor('--diterima'),
                    themeColor('--diproses'),
                    themeColor('--selesai'),
                    themeColor('--ditolak')
                ],
                borderWidth: 4,
                borderColor: themeColor('--surface'),
                hoverOffset: 8
            }]
        };

        const barData = {
            labels: {!! json_encode($labelBulan) !!},
            datasets: [{
                label: 'Laporan',
                data: {!! json_encode($dataBulan) !!},
                backgroundColor: themeAlpha('--diterima', 0.45),
                borderColor: themeColor('--diterima'),
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 28,
                maxBarThickness: 34
            }]
        };

        Chart.defaults.font.family = "'Inter', 'Public Sans', sans-serif";
        Chart.defaults.color = themeColor('--ink-soft');
        Chart.defaults.borderColor = themeColor('--line');

        if (donut) donut.destroy();
        if (bar) bar.destroy();

        donut = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: donutData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: tooltip()
                },
                animation: { animateScale: true, animateRotate: true }
            }
        });

        bar = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: tooltip()
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1 },
                        grid: { color: themeColor('--accent-tint') }
                    }
                }
            }
        });
    }

    function start() {
        if (!window.SerojapTheme) return;
        drawCharts();
        window.SerojapTheme.onThemeChange(drawCharts);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
</script>

@endsection