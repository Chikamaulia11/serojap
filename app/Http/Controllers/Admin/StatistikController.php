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
                ->joinSub(
                    $latestIds,
                    'latest',
                    'tabel_status.id_status',
                    '=',
                    'latest.id_status'
                )
                ->where('tabel_status.status', $status)
                ->count();
        };

        $diterima = $countByStatus('diterima');
        $proses   = $countByStatus('diproses');
        $selesai  = $countByStatus('selesai');
        $ditolak  = $countByStatus('ditolak');

        // LAPORAN PER BULAN
        $perBulan = collect();

        try {
            // MONTH()/YEAR() hanya ada di MySQL, jadi untuk driver lain
            // dipakai padanan strftime(). Hasilnya sama-sama angka bulan/tahun.
            [$bulanSql, $tahunSql] = match (DB::connection()->getDriverName()) {
                'sqlite' => [
                    "CAST(STRFTIME('%m', created_at) AS INTEGER)",
                    "CAST(STRFTIME('%Y', created_at) AS INTEGER)",
                ],
                'pgsql' => [
                    'CAST(EXTRACT(MONTH FROM created_at) AS INTEGER)',
                    'CAST(EXTRACT(YEAR FROM created_at) AS INTEGER)',
                ],
                default => ['MONTH(created_at)', 'YEAR(created_at)'],
            };

            $perBulan = Report::select(
                    DB::raw($bulanSql . ' as bulan'),
                    DB::raw($tahunSql . ' as tahun'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->whereYear('created_at', now()->year)
                ->groupBy('bulan', 'tahun')
                ->orderBy('bulan')
                ->get();
        } catch (\Throwable $e) {
            $perBulan = collect();
        }

        $labelBulan = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des',
        ];

        $dataBulan = array_fill(0, 12, 0);

        foreach ($perBulan as $row) {
            $dataBulan[$row->bulan - 1] = $row->jumlah;
        }

        return view('admin.statistik.index', compact(
            'total',
            'baru',
            'diterima',
            'proses',
            'selesai',
            'ditolak',
            'labelBulan',
            'dataBulan'
        ));
    }
}