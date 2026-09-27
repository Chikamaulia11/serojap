<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Support\LaporanStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ReportController extends Controller
{
    // =========================
    // FORM LAPORAN
    // =========================
    public function create()
    {
        return view('pelapor.form', [
            // Nama pelapor sudah diketahui dari akun, jadi tidak perlu
            // diketik ulang setiap kali membuat laporan.
            'namaDefault' => Auth::user()?->name,
        ]);
    }

    // =========================
    // SIMPAN LAPORAN
    // =========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'keterangan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'nama.required' => 'Nama pelapor wajib diisi supaya petugas bisa menghubungi kalau perlu klarifikasi.',
            'nama.max' => 'Nama pelapor maksimal 255 karakter.',
            'foto.required' => 'Foto kerusakan wajib diunggah supaya petugas bisa melihat kondisinya.',
            'foto.mimes' => 'Foto harus berformat JPG atau PNG. Kalau foto dari iPhone berformat HEIC, ganti pengaturan kamera ke "Most Compatible" lalu ambil ulang.',
            'foto.max' => 'Ukuran foto maksimal 5 MB. Foto_resolution tinggi bisa dikecilkan dulu sebelum diunggah.',
            'latitude.required' => 'Lokasi belum ditandai. Tekan "Tentukan Lokasi di Peta", lalu ketuk lokasi kerusakan pada peta.',
            'latitude.numeric' => 'Lokasi belum ditandai. Tekan "Tentukan Lokasi di Peta", lalu ketuk lokasi kerusakan pada peta.',
            'longitude.required' => 'Lokasi belum ditandai. Tekan "Tentukan Lokasi di Peta", lalu ketuk lokasi kerusakan pada peta.',
            'longitude.numeric' => 'Lokasi belum ditandai. Tekan "Tentukan Lokasi di Peta", lalu ketuk lokasi kerusakan pada peta.',
            'keterangan.min' => 'Keterangan minimal 10 karakter. Tuliskan misalnya jenis kerusakan dan sejak kapan.',
        ], [
            'nama' => 'nama pelapor',
            'foto' => 'foto kerusakan',
            'alamat' => 'alamat lokasi',
            'latitude' => 'lintang lokasi',
            'longitude' => 'bujur lokasi',
            'keterangan' => 'keterangan',
        ]);

        // =========================
        // BATAS PERCOBAAN KIRIM
        //
        // Sebelumnya throttle:5,1 dipasang sebagai middleware di
        // route, sehingga middleware berjalan SEBELUM validasi dan
        // setiap percobaan gagal validasi ikut menghabiskan kuota.
        // Pengguna yang memperbaiki 3 kesalahan dua kali langsung
        // kena HTTP 429 tanpa tahu kenapa.
        //
        // Di sini validasinya TIDAK dihitung sebagai percobaan, tapi
        // setiap laporan yang benar-benar tersimpan AKAN dihitung --
        // inilah gunanya batas ini: menahan flood laporan, bukan
        // menghukum pengguna yang masih memperbaiki inputnya.
        //
        // Catatan bug yang diperbaiki: versi lama memanggil
        // `tooManyAttempts()` tanpa pernah `hit()`, lalu `clear()`
        // setiap kali berhasil. Hasilnya penghitungannya tidak pernah
        // pernah bertambah, jadi limiter ini tidak membatasi apa pun.
        // =========================
        $kunci = $this->kunciBatasKirim($request);

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);

            // 429, bukan 422: ini batas laju, bukan isian form yang
            // salah. Halaman 429 yang menjelaskan cara melanjutkan
            // sudah ada di resources/views/errors/429.blade.php.
            abort(429, 'Terlalu banyak laporan dikirim dari perangkat ini. Tunggu ' . $detik . ' detik lalu coba lagi. Kalau laporan kamu belum terkirim, cek dulu Riwayat Saya.');
        }

        $foto = $request->file('foto')->store('reports', 'public');

        try {
            // `DB::transaction()` mengembalikan nilai balik closure-nya.
            // Variabel yang dibuat di dalam closure TIDAK terlihat dari
            // luar, jadi `$report` harus dikembalikan secara eksplisit
            // -- kalau tidak, pemanggilan `nomor_referensi` di bawah
            // akan membaca variabel yang tidak ada.
            $report = DB::transaction(function () use ($validated, $foto) {
                $laporan = Report::create([
                    'user_id' => Auth::id(),
                    // `nama` sudah `required`, jadi tidak perlu fallback ke
                    // nama akun. Form sudah mem-prefill nilai ini dari
                    // `auth()->user()->name`.
                    'nama_pelapor' => trim($validated['nama']),
                    'foto' => $foto,
                    'alamat' => trim($validated['alamat']),
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'keterangan' => trim($validated['keterangan']),
                ]);

                TabelStatus::create([
                    'report_id' => $laporan->id,
                    'user_id' => Auth::id(),
                    'status' => 'diterima',
                    'keterangan' => 'Laporan berhasil dikirim dan masuk antrean petugas.',
                ]);

                return $laporan;
            });
        } catch (Throwable $e) {
            // Foto sudah tersimpan ke storage sebelum insert. Kalau
            // insert-nya gagal, hapus lagi -- kalau tidak, storage
            // akan penuh file yatim dari laporan yang tidak pernah
            // benar-benar ada.
            if ($foto && Storage::disk('public')->exists($foto)) {
                Storage::disk('public')->delete($foto);
            }

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Laporan gagal disimpan karena ada masalah di server. Foto kamu sudah tidak diunggah. Coba kirim lagi beberapa saat lagi.');
        }

        // PENTING: inilahKENAPA limiter bekerja. `hit()` menambah
        // penghitung, sedangkan `clear()` (yang dulu ada di sini)
        // justru meresetnya setiap kali berhasil.
        RateLimiter::hit($kunci, 60);

        return redirect()
            ->route('laporan.my-report')
            ->with('success', 'Laporan berhasil dikirim dengan nomor ' . $report->nomor_referensi . '. Kamu bisa pantau progresnya di Riwayat Laporan.');
    }

    // =========================
    // RIWAYAT LAPORAN USER
    // =========================
    public function myReport(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(Report::STATUS))],
            'q' => ['nullable', 'string', 'max:100'],
        ], [
            'status.in' => 'Filter status tidak dikenal.',
        ], [
            'status' => 'status',
            'q' => 'pencarian',
        ]);

        $cari = trim((string) ($validated['q'] ?? ''));

        $reports = Report::with([
                'latestStatus',
                'statuses.admin',
            ])
            ->where('user_id', Auth::id())
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('latestStatus', function ($statusQuery) use ($request) {
                    $statusQuery->where('status', $request->status);
                });
            })
            ->when($cari !== '', function ($query) use ($cari) {
                // Nomor referensipelapor dimulai dengan SRJ-, jadi
                // "SRJ-2026-12" harus bisa menemukan laporannya.
                $query->where(function ($inner) use ($cari) {
                    $inner->where('alamat', 'like', '%' . $cari . '%')
                        ->orWhere('keterangan', 'like', '%' . $cari . '%')
                        ->orWhere('nama_pelapor', 'like', '%' . $cari . '%')
                        ->orWhereRaw('LOWER(alamat) LIKE ?', ['%' . mb_strtolower($cari) . '%']);

                    if (ctype_digit($cari)) {
                        $inner->orWhere('id', (int) $cari);
                    }

                    if (preg_match('/(\d{4})-?(\d+)/', $cari, $m)) {
                        $inner->orWhere('id', (int) $m[2]);
                    }
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $ringkasan = $this->ringkasanPengguna(Auth::id());

        return view('pelapor.riwayat', compact('reports', 'ringkasan'));
    }

    /**
     * Jumlah laporan pengguna per status.
     */
    private function ringkasanPengguna(int $userId): array
    {
        $latestIds = LaporanStats::subqueryStatusTerbaru();

        $count = fn (string $status): int => DB::table('reports')
            ->joinSub($latestIds, 'latest', 'tabel_status.report_id', '=', 'reports.id')
            ->join('tabel_status', 'tabel_status.id_status', '=', 'latest.id_status')
            ->where('reports.user_id', $userId)
            ->where('tabel_status.status', $status)
            ->count();

        return [
            'total' => Report::where('user_id', $userId)->count(),
            'diterima' => $count('diterima'),
            'diproses' => $count('diproses'),
            'selesai' => $count('selesai'),
            'ditolak' => $count('ditolak'),
        ];
    }

    /**
     * Kunci rate limiter untuk batas kirim laporan.
     */
    private function kunciBatasKirim(Request $request): string
    {
        return 'kirim-laporan|' . Auth::id() . '|' . $request->ip();
    }
}
