<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TabelFaq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Jumlah laporan publik terbaru yang ditampilkan di landing page.
     */
    private const JUMLAH_LAPORAN_PUBLIK = 9;

    /**
     * Panjang maksimal potongan keterangan pada kartu laporan publik.
     */
    private const PANJANG_KETERANGAN = 120;

    /**
     * Jumlah FAQ dari database yang ditambahkan setelah FAQ dasar.
     */
    private const JUMLAH_FAQ_TAMBAHAN = 5;

    /**
     * =========================
     * LANDING PAGE PUBLIK
     * =========================
     */
    public function home(): View
    {
        $totalLaporan = Report::count();

        $jumlahPerStatus = $this->jumlahLaporanPerStatus();

        $rataRataHariPenanganan = $this->rataRataLamaPenanganan();

        $laporanTerbaru = $this->laporanPublikTerbaru();

        $faq = $this->faqPublik();

        return view('welcome', compact(
            'totalLaporan',
            'jumlahPerStatus',
            'rataRataHariPenanganan',
            'laporanTerbaru',
            'faq'
        ));
    }

    /**
     * =========================
     * JUMLAH LAPORAN PER STATUS TERBARU
     *
     * Pakai subquery MAX(id_status) per report_id supaya status yang dihitung
     * adalah status terakhir tiap laporan (pola yang sama dengan
     * Admin\StatistikController, tanpa N+1).
     * =========================
     */
    private function jumlahLaporanPerStatus(): array
    {
        $latestIds = DB::table('tabel_status')
            ->select(DB::raw('MAX(id_status) as id_status'))
            ->groupBy('report_id');

        $hitung = function (string $status) use ($latestIds): int {
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

        return [
            'diterima' => $hitung('diterima'),
            'diproses' => $hitung('diproses'),
            'selesai' => $hitung('selesai'),
            'ditolak' => $hitung('ditolak'),
        ];
    }

    /**
     * =========================
     * RATA-RATA LAMA PENANGANAN (HARI)
     *
     * Selisih antara waktu laporan dibuat dan waktu status 'selesai' dicatat.
     * Dihitung di database, bukan dengan looping di PHP.
     * =========================
     */
    private function rataRataLamaPenanganan(): ?float
    {
        $selisihDetik = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%s', tabel_status.created_at)"
                . " - strftime('%s', reports.created_at)",
            'pgsql' => 'EXTRACT(EPOCH FROM (tabel_status.created_at - reports.created_at))',
            default => 'UNIX_TIMESTAMP(tabel_status.created_at)'
                . ' - UNIX_TIMESTAMP(reports.created_at)',
        };

        $rataRata = DB::table('tabel_status')
            ->join('reports', 'reports.id', '=', 'tabel_status.report_id')
            ->where('tabel_status.status', 'selesai')
            ->avg(DB::raw('(' . $selisihDetik . ') / 86400'));

        if ($rataRata === null) {
            return null;
        }

        return round((float) $rataRata, 1);
    }

    /**
     * =========================
     * LAPORAN PUBLIK TERBARU
     *
     * Hanya kolom yang aman untuk ditampilkan publik yang diambil di level
     * query: nama_pelapor, foto, dan user_id tidak pernah masuk ke view.
     * =========================
     */
    private function laporanPublikTerbaru()
    {
        return Report::select([
                'id',
                'alamat',
                'keterangan',
                'created_at',
            ])
            ->with('latestStatus')
            ->latest()
            ->limit(self::JUMLAH_LAPORAN_PUBLIK)
            ->get()
            ->map(function (Report $laporan) {
                $laporan->keterangan_pendek = Str::limit(
                    (string) $laporan->keterangan,
                    self::PANJANG_KETERANGAN
                );

                return $laporan;
            });
    }

    /**
     * =========================
     * FAQ PUBLIK
     *
     * FAQ dasar selalu ditampilkan supaya halaman tetap informatif aunque
     * tabel FAQ masih kosong, lalu ditambahkan FAQ yang dikelola admin.
     * =========================
     */
    private function faqPublik()
    {
        $faqDasar = [
            [
                'pertanyaan' => 'Apakah saya perlu akun untuk melapor jalan rusak?',
                'jawaban' => 'Ya. Untuk mengirim laporan baru Anda perlu mendaftar '
                    . 'sebagai pelapor terlebih dahulu. Namun seluruh laporan publik '
                    . 'di halaman beranda tetap bisa Anda lihat tanpa login.',
            ],
            [
                'pertanyaan' => 'Berapa lama laporan saya diproses?',
                'jawaban' => 'Lamanya tergantung kondisi jalan dan volume pekerjaan. '
                    . 'Rata-rata penanganan di widget statistik beranda adalah gambaran '
                    . 'waktu penanganan laporan yang sudah selesai diperbaiki.',
            ],
            [
                'pertanyaan' => 'Apakah data pribadi saya aman?',
                'jawaban' => 'Nama pelapor tidak pernah ditampilkan di halaman publik. '
                    . 'Foto laporan hanya dapat dilihat petugas dan pelapor yang '
                    . 'mengirimnya, sedangkan identitas pelapor disimpan terbatas '
                    . 'untuk keperluan verifikasi.',
            ],
            [
                'pertanyaan' => 'Bagaimana cara cek status laporan saya sendiri?',
                'jawaban' => 'Setelah login sebagai pelapor, buka menu riwayat laporan. '
                    . 'Di sana Anda bisa melihat status terakhir beserta catatan dari '
                    . 'petugas pada setiap perubahan status.',
            ],
            [
                'pertanyaan' => 'Jenis kerusakan apa saja yang bisa dilaporkan?',
                'jawaban' => 'Lubang, jalan berlubang dan berlantai, retak atau '
                    . 'terbelah, permukaan tidak rata, genangan air, serta jalan atau '
                    . 'jembatan yang rusak dan mengganggu kelancaran lalu lintas.',
            ],
        ];

        $faqTambahan = TabelFaq::query()
            ->orderBy('urutan')
            ->orderBy('id_faq')
            ->limit(self::JUMLAH_FAQ_TAMBAHAN)
            ->get([
                'id_faq',
                'pertanyaan',
                'jawaban',
            ])
            ->map(fn (TabelFaq $faq) => (object) $faq->only(['pertanyaan', 'jawaban']));

        return collect($faqDasar)
            ->map(fn (array $faq) => (object) $faq)
            ->concat($faqTambahan)
            ->values();
    }
}
