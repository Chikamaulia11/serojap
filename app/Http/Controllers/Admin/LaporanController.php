<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\TabelStatus;
use App\Support\LaporanStats;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    /**
     * =========================
     * DAFTAR LAPORAN
     * =========================
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Report::STATUS))],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'sort' => ['nullable', Rule::in(['terbaru', 'terlama', 'status-tertinggi'])],
        ], [], [
            'search' => 'pencarian',
            'status' => 'status',
            'dari' => 'tanggal mulai',
            'sampai' => 'tanggal akhir',
            'sort' => 'urutan',
        ]);

        $laporan = $this->queryLaporan($request)
            ->paginate((int) $request->input('per_page', 10))
            ->withQueryString();

        $stats = $this->statistikStatus();

        return view(
            'admin.laporan.index',
            compact('laporan', 'stats')
        );
    }

    /**
     * =========================
     * DETAIL LAPORAN
     * =========================
     */
    public function show(string $id)
    {
        $laporan = Report::with([
            'user',
            'statuses.admin',
            'latestStatus',
        ])->findOrFail($id);

        /*
         * `created_at` wajib ikut diselect: `nomor_referensi` adalah
         * accessor yang membentuk "SRJ-{tahun}-{id}", jadi tanpa kolom
         * ini tahun pada nomor acuan akan diam-diam memakai tahun
         * berjalan dan laporan tahun lalu salah tahun.
         */
        $daftarLaporan = Report::select('id', 'alamat', 'created_at')
            ->latest()
            ->limit(200)
            ->get();

        return view('admin.laporan.show', [
            'laporan' => $laporan,
            'daftarLaporan' => $daftarLaporan,
        ]);
    }

    /**
     * =========================
     * UPDATE STATUS
     * =========================
     */
    public function update(Request $request, string $id)
    {
        $laporan = Report::with('latestStatus')->findOrFail($id);

        $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::STATUS))],
            'keterangan' => ['required', 'string', 'min:5', 'max:1000'],
            'foto_perbaikan' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'status.required' => 'Pilih status baru terlebih dahulu.',
            'keterangan.min' => 'Keterangan minimal 5 karakter agar pelapor tahu apa yang terjadi.',
            'foto_perbaikan.mimes' => 'Foto perbaikan harus berformat JPG atau PNG.',
            'foto_perbaikan.max' => 'Ukuran foto perbaikan maksimal 2 MB.',
        ], [
            'status' => 'status',
            'keterangan' => 'keterangan',
            'foto_perbaikan' => 'foto perbaikan',
        ]);

        $statusBaru = $request->string('status')->toString();
        $statusLama = $laporan->latestStatus?->status;

        $fotoPerbaikan = null;

        if ($request->hasFile('foto_perbaikan')) {
            $fotoPerbaikan = $request
                ->file('foto_perbaikan')
                ->store('perbaikan', 'public');
        }

        TabelStatus::create([
            'report_id' => $laporan->id,
            'user_id' => auth()->id(),
            'status' => $statusBaru,
            'keterangan' => $request->string('keterangan')->toString(),
            'foto_perbaikan' => $fotoPerbaikan,
        ]);

        /*
         * Status yang sama TIDAK lagi ditolak.
         *
         * `tabel_status` bersifat append-only: setiap baris adalah satu
         * entri riwayat, bukan penimpaan. Duty officer sering perlu
         * menulis catatan tambahan tanpa mengubah status -- misalnya
         * "sudah dikerjakan, menunggu material Shows" -- dan balas
         * submission seperti itu akan selalu ditolak begitu saja.
         *
         * Karena itu baris tetap dibuat; hanya kalimat pesan suksesnya
         * yang menyesuaikan.
         */
        $pesan = $statusBaru === $statusLama
            ? 'Catatan progres untuk laporan #' . $laporan->nomor_referensi
                . ' tersimpan. Statusnya tetap "' . Report::STATUS[$statusBaru] . '".'
            : 'Status laporan #' . $laporan->nomor_referensi
                . ' berhasil diubah dari "' . Report::STATUS[$statusLama] . '" ke "'
                . Report::STATUS[$statusBaru] . '".';

        return redirect()
            ->route('admin.laporan.show', $laporan->id)
            ->with('success', $pesan);
    }

    /**
     * =========================
     * HALAMAN UPDATE STATUS CEPAT
     *
     * Halaman ini memilih laporan lalu langsung menampilkan form
     * ubah status, supaya petugas tidak perlu klik "Daftar Laporan"
     * dulu hanya untuk menemukan satu laporan.
     * =========================
     */
    public function updateStatusIndex(Request $request)
    {
        $daftarLaporan = Report::query()
            ->when($request->search, function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('alamat', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->with('latestStatus')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $terpilihId = $request->integer('laporan') ?: null;

        $laporan = null;

        if ($terpilihId) {
            $laporan = Report::with(['user', 'statuses.admin', 'latestStatus'])
                ->find($terpilihId);

            if (! $laporan) {
                return back()->with('error', 'Laporan yang dipilih tidak ditemukan.');
            }
        }

        $statistik = $this->statistikStatus();

        return view('admin.laporan.update-status', compact(
            'daftarLaporan',
            'laporan',
            'statistik'
        ));
    }

    /**
     * =========================
     * RIWAYAT STATUS
     * =========================
     */
    public function riwayatStatusIndex(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Report::STATUS))],
        ], [], [
            'search' => 'pencarian',
            'status' => 'status',
        ]);

        $riwayat = TabelStatus::with([
            'laporan',
            'admin',
        ])

            // =========================
            // SEARCH
            //
            // `keterangan` yang dicari adalah milik PETUGAS (kolom di
            // tabel_status), bukan keterangan pelapor. Sebelumnya
            // orWhere-nya menunjuk ke reports.keterangan sehingga
            // pencarian catatan petugas selalu mengembalikan nol
            // hasil tanpa error yang terlihat.
            // =========================
            ->when($request->search, function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('laporan', function ($laporanQuery) use ($search) {
                            $laporanQuery->where('alamat', 'like', "%{$search}%");
                        });
                });
            })

            ->when($request->status, fn ($query) => $query->where('status', $request->status))

            ->latest('id_status')

            ->paginate((int) $request->input('per_page', 15))
            ->withQueryString();

        return view(
            'admin.laporan.riwayat-status',
            compact('riwayat')
        );
    }

    /**
     * Query daftar laporan + filter.
     */
    private function queryLaporan(Request $request)
    {
        $sort = $request->input('sort', 'terbaru');

        $query = Report::with(['user', 'latestStatus'])

            // =========================
            // SEARCH
            //
            // Mencari alamat/keterangan saja membuat admin tidak bisa
            // menemukan laporan dari nomor referensi atau dari nama/email
            // pelapor, padahal keduanya tampil di tabel.
            // =========================
            ->when($request->search, function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($sub) use ($search) {
                    $sub->where('alamat', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })

            // =========================
            // FILTER STATUS (status TERBARU laporan)
            // =========================
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('latestStatus', function ($statusQuery) use ($request) {
                    $statusQuery->where('status', $request->status);
                });
            })

            // =========================
            // FILTER RANGE TANGGAL
            // =========================
            ->when($request->dari, fn ($query) => $query->whereDate('created_at', '>=', $request->dari))
            ->when($request->sampai, fn ($query) => $query->whereDate('created_at', '<=', $request->sampai));

        return match ($sort) {
            'terlama' => $query->oldest(),
            'status-tertinggi' => $query->orderByRaw(
                "CASE (SELECT status FROM tabel_status WHERE tabel_status.report_id = reports.id ORDER BY id_status DESC LIMIT 1)
                 WHEN 'ditolak' THEN 0 WHEN 'diterima' THEN 1 WHEN 'diproses' THEN 2 ELSE 3 END ASC"
            )->latest(),
            default => $query->latest(),
        };
    }

    /**
     * Jumlah laporan per status, berdasarkan status TERBARU setiap
     * laporan. Satu sumber angka ini dipakai kartu statistik di daftar
     * laporan, dashboard admin, dan halaman update status, supaya
     * angka di satu halaman tidak bertentangan dengan halaman lain.
     */
    private function statistikStatus(): array
    {
        $ringkasan = LaporanStats::ringkasan();

        return [
            'total' => $ringkasan['total'],
            'baru' => $ringkasan['baru'],
            'diterima' => $ringkasan['diterima'],
            'diproses' => $ringkasan['diproses'],
            'selesai' => $ringkasan['selesai'],
            'ditolak' => $ringkasan['ditolak'],
            'perluTindakan' => LaporanStats::perluTindakan(3),
        ];
    }
}
