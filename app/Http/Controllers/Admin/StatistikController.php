<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Support\LaporanStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // TAHUN YANG DIPILIH
        // =========================
        // Default tahun berjalan, tapi admin bisa memilih tahun lain.
        // Sebelumnya grafik hanya menampilkan tahun berjalan dengan
        // begitu saja, sehingga laporan tahun lalu lenyap dari grafik.
        $daftarTahun = Report::query()
            ->selectRaw('DISTINCT ' . $this->sqlTahun('created_at') . ' as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($t) => (int) $t)
            ->all();

        if ($daftarTahun === []) {
            $daftarTahun = [(int) now()->year];
        }

        $tahun = (int) $request->input('tahun', $daftarTahun[0]);

        if (! in_array($tahun, $daftarTahun, true)) {
            $tahun = $daftarTahun[0];
        }

        // =========================
        // TOTAL LAPORAN TAHUN INI
        // =========================
        $total = Report::whereYear('created_at', $tahun)->count();

        $totalKeseluruhan = Report::count();

        // =========================
        // JUMLAH PER STATUS (STATUS TERBARU)
        // =========================
        $ringkasan = LaporanStats::ringkasan($tahun);

        $total = $ringkasan['total'];
        $baru = $ringkasan['baru'];
        $diterima = $ringkasan['diterima'];
        $proses = $ringkasan['diproses'];
        $selesai = $ringkasan['selesai'];
        $ditolak = $ringkasan['ditolak'];
        $rasioSelesai = $ringkasan['rasioSelesai'];

        // =========================
        // LAPORAN PER BULAN
        // =========================
        $perBulan = Report::select(
            DB::raw($this->sqlBulan('created_at') . ' as bulan'),
            DB::raw('COUNT(*) as jumlah')
        )
            ->whereYear('created_at', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('jumlah', 'bulan');

        $labelBulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        $dataBulan = [];

        for ($i = 1; $i <= 12; $i++) {
            $dataBulan[] = (int) ($perBulan[$i] ?? 0);
        }

        // =========================
        // LOCASI TERPADAT
        // =========================
        $lokasiTerpadat = Report::query()
            ->whereYear('created_at', $tahun)
            ->select('alamat', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('alamat')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        // =========================
        // RASIO PENYELESAIAN
        // =========================
        return view('admin.statistik.index', compact(
            'total',
            'totalKeseluruhan',
            'baru',
            'diterima',
            'proses',
            'selesai',
            'ditolak',
            'rasioSelesai',
            'labelBulan',
            'dataBulan',
            'lokasiTerpadat',
            'tahun',
            'daftarTahun'
        ));
    }

    /**
     * Ekspresi SQL untuk mengambil nomor bulan, lintas driver.
     */
    private function sqlBulan(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(STRFTIME('%m', {$column}) AS INTEGER)",
            'pgsql' => "CAST(EXTRACT(MONTH FROM {$column}) AS INTEGER)",
            default => "MONTH({$column})",
        };
    }

    /**
     * Ekspresi SQL untuk mengambil tahun, lintas driver.
     */
    private function sqlTahun(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(STRFTIME('%Y', {$column}) AS INTEGER)",
            'pgsql' => "CAST(EXTRACT(YEAR FROM {$column}) AS INTEGER)",
            default => "YEAR({$column})",
        };
    }
}
