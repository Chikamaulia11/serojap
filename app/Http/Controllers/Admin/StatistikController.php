<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

use App\Models\TabelStatus;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        // Total laporan
        $total = Report::count();


        // Laporan belum ada status (baru)
        $baru = Report::doesntHave('statuses')->count();


        // Cari status terbaru per laporan (berdasarkan created_at).
        // Pada relasi Report::latestStatus(), TabelStatus diurutkan dengan created_at terbaru.
        $perStatusRows = TabelStatus::query()
            ->select('report_id', 'status', DB::raw('MAX(id_status) as latestId'))
            ->groupBy('report_id', 'status')
            ->get();

        // Cara aman: pakai relasi latestStatus() di Collection (lebih lambat, tapi mencegah error relasi/kolom).
        // Kalau datanya besar nanti bisa dioptimasi.
        $reports = Report::select('id')->get();
        $latestPerReport = $reports->map(function ($report) {
            return $report->latestStatus?->status;
        })->filter();

        $perStatus = $latestPerReport->countBy()->toArray();

        $diterima = $perStatus['diterima'] ?? 0;
        $proses   = $perStatus['proses']   ?? 0;
        $selesai  = $perStatus['selesai']  ?? 0;
        $ditolak  = $perStatus['ditolak']  ?? 0;


        // Laporan per bulan
        // Pada beberapa environment, kolom `created_at` di `tabel_laporan` bisa tidak ada.
        // Gunakan `id_laporan` untuk mengurutkan kemunculan, lalu hitung per bulan
        // dari `created_at` jika tersedia.
        // Jika `created_at` tidak ada, fallback: tampilkan 12 slot 0.
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


        // Siapkan data untuk chart bulanan (12 slot)
        $labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataBulan  = array_fill(0, 12, 0);
        foreach ($perBulan as $row) {
            $dataBulan[$row->bulan - 1] = $row->jumlah;
        }

        // Laporan per kecamatan (top 5)
        // Kolom kecamatan tidak selalu ada di tabel `reports` (tergantung migrasi).
        // Jika tidak ada, fallback ke kosong agar tidak error.
        $perKecamatan = collect();
        try {
            if (Schema::hasColumn('reports', 'kecamatan')) {
                $perKecamatan = Report::select('kecamatan', DB::raw('COUNT(*) as jumlah'))
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