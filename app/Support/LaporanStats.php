<?php

namespace App\Support;

use App\Models\Report;
use Illuminate\Support\Facades\DB;

/**
 * Satu sumber angka untuk status laporan.
 *
 * Sebelumnya tiap halaman menghitung sendiri dengan subquery
 * MAX(id_status). Akibatnya angka "total" dan "baru" berbeda antar
 * halaman: satu menandai laporan tanpa status sebagai "Diterima",
 * yang lain menghitungnya "Baru", dan kartu statistik di daftar
 * laporan memakai definisi ketiga. Semua halaman sekarang memakai
 * kelas ini supaya tidak ada dua halaman yang saling bertentangan.
 */
class LaporanStats
{
    /**
     * Subquery: id_status terbaru untuk setiap laporan.
     *
     * WAJIB harus jadi pasangan dengan `Report::latestStatus()`, yang
     * juga memakai `MAX(id_status)`. Kalau aturannya berbeda, kartu
     * statistik menghitung laporan "Selesai" sementara badge di daftar
     * laporan masih menampilkan "Diproses" untuk laporan yang sama --
     * persis kelas bug yang kelas ini dibuat untuk dihilangkan.
     */
    public static function subqueryStatusTerbaru()
    {
        return DB::table('tabel_status')
            ->select(DB::raw('MAX(id_status) as id_status'))
            ->groupBy('report_id');
    }

    /**
     * Jumlah laporan dengan status terbaru tertentu.
     */
    public static function countByStatus(string $status, ?int $tahun = null): int
    {
        $latestIds = self::subqueryStatusTerbaru();

        $query = DB::table('tabel_status')
            ->joinSub($latestIds, 'latest', 'tabel_status.id_status', '=', 'latest.id_status')
            ->where('tabel_status.status', $status);

        if ($tahun !== null) {
            $query->whereIn('tabel_status.report_id', self::queryIdLaporanTahun($tahun));
        }

        return $query->count();
    }

    /**
     * Ringkasan seluruh laporan, atau khusus satu tahun.
     *
     * @return array{total:int, baru:int, diterima:int, diproses:int, selesai:int, ditolak:int, diprosesPersen:float, selesaiPersen:float}
     */
    public static function ringkasan(?int $tahun = null): array
    {
        $total = $tahun === null
            ? Report::count()
            : Report::whereYear('created_at', $tahun)->count();

        $diterima = self::countByStatus('diterima', $tahun);
        $diproses = self::countByStatus('diproses', $tahun);
        $selesai = self::countByStatus('selesai', $tahun);
        $ditolak = self::countByStatus('ditolak', $tahun);

        $berstatus = $diterima + $diproses + $selesai + $ditolak;
        $dikerjakan = $diproses + $selesai;

        return [
            'total' => $total,
            // Laporan yang belum pernah disentuh petugas. Dihitung dari
            // selisih: `doesntHave('statuses')` selalu 0 karena laporan
            // baru langsung dibuat bersama baris status awalnya.
            'baru' => max(0, $total - $berstatus),
            'diterima' => $diterima,
            'diproses' => $diproses,
            'selesai' => $selesai,
            'ditolak' => $ditolak,
            'dikerjakan' => $dikerjakan,
            'rasioSelesai' => $berstatus > 0 ? round(($selesai / $berstatus) * 100, 1) : 0.0,
            'rasioDikerjakan' => $total > 0 ? round(($dikerjakan / $total) * 100, 1) : 0.0,
        ];
    }

    /**
     * Jumlah laporan yang belum pernah diubah statusnya oleh petugas
     * dalam N hari terakhir. Dipakai untuk kartu "Perlu Tindakan".
     */
    public static function perluTindakan(int $hari = 3): int
    {
        $latestIds = self::subqueryStatusTerbaru();

        return Report::query()
            ->where('created_at', '<=', now()->subDays($hari))
            ->whereIn('id', DB::table('tabel_status')
                ->joinSub($latestIds, 'latest', 'tabel_status.id_status', '=', 'latest.id_status')
                ->whereIn('tabel_status.status', ['diterima', 'diproses'])
                ->select('tabel_status.report_id'))
            ->count();
    }

    /**
     * Subquery id laporan yang dibuat pada tahun tertentu.
     */
    private static function queryIdLaporanTahun(int $tahun)
    {
        return Report::query()
            ->whereYear('created_at', $tahun)
            ->select('id');
    }
}
