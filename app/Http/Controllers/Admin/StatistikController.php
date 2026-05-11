<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabelLaporan;
use App\Models\TabelStatus;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function index()
    {
        // Total laporan
        $total = TabelLaporan::count();

        // Laporan belum ada status (baru)
        $baru = TabelLaporan::doesntHave('statuses')->count();

        // Cari status terbaru per laporan tanpa bergantung pada kolom timestamp.
        // Gunakan `id_status` (auto-increment) sebagai penentu status terakhir.
        $perStatus = TabelStatus::query()
            ->select('id_laporan', DB::raw('MAX(id_status) as latestId'))
            ->groupBy('id_laporan')
            ->get()
            ->map(function ($row) {
                return TabelStatus::query()
                    ->where('id_laporan', $row->id_laporan)
                    ->where('id_status', $row->latestId)
                    ->value('status');
            })
            ->countBy()
            ->toArray();


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
            $perBulan = TabelLaporan::select(
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
        $perKecamatan = TabelLaporan::select('kecamatan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kecamatan')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return view('admin.statistik.index', compact(
            'total', 'baru', 'diterima', 'proses', 'selesai', 'ditolak',
            'labelBulan', 'dataBulan', 'perKecamatan'
        ));
    }
}