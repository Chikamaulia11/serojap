<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        // TOTAL SEMUA LAPORAN
        $total = Report::count();

        // BARU: laporan yang belum punya status sama sekali
        $baru = Report::doesntHave('statuses')->count();

        // SUBQUERY: ambil id_status terbaru (MAX) per report_id
        $latestIds = DB::table('tabel_status')
            ->select(DB::raw('MAX(id_status) as id_status'))
            ->groupBy('report_id');

        $countByStatus = function (string $status) use ($latestIds): int {
            return DB::table('tabel_status')
                ->joinSub($latestIds, 'latest', 'tabel_status.id_status', '=', 'latest.id_status')
                ->where('tabel_status.status', $status)
                ->count();
        };

        $diterima = $countByStatus('diterima');
        $proses   = $countByStatus('diproses');
        $selesai  = $countByStatus('selesai');
        $ditolak  = $countByStatus('ditolak');

        // LAPORAN PER BULAN (chart bar)
        $perBulan = collect();
        try {
            $perBulan = Report::select(
                    DB::raw('MONTH(created_at) as bulan'),
                    DB::raw('YEAR(created_at) as tahun'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->whereYear('created_at', now()->year)
                ->groupBy('tahun', 'bulan')
                ->orderBy('bulan')
                ->get();
        } catch (\Throwable $e) {
            $perBulan = collect();
        }

        $labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataBulan  = array_fill(0, 12, 0);
        foreach ($perBulan as $row) {
            $dataBulan[$row->bulan - 1] = $row->jumlah;
        }

        // TOP KECAMATAN (top 5)
        $perKecamatan = collect();
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing('reports');
            if (in_array('kecamatan', $columns)) {
                $perKecamatan = Report::select('kecamatan', DB::raw('COUNT(*) as jumlah'))
                    ->whereNotNull('kecamatan')
                    ->where('kecamatan', '!=', '')
                    ->groupBy('kecamatan')
                    ->orderByDesc('jumlah')
                    ->limit(5)
                    ->get();
            }
        } catch (\Throwable $e) {
            $perKecamatan = collect();
        }

        return view('admin.statistik.index', compact(
            'total', 'baru', 'diterima', 'proses', 'selesai', 'ditolak',
            'labelBulan', 'dataBulan', 'perKecamatan'
        ));
    }
}