<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /* =========================
       TABLE
    ========================= */
    protected $table = 'reports';

    /* =========================
       FILLABLE

       Kolom `status` sengaja tidak ada: statusnya hidup di tabel
       `tabel_status` (riwayat), bukan di tabel `reports`. Kalau
       kolom dihapus dari database tapi tetap fillable, satu
       `Report::create($request->all())` akan mencoba menulis kolom
       yang sudah tidak ada.
     ========================= */
    protected $fillable = [
        'user_id',
        'nama_pelapor',
        'foto',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
    ];

    /* =========================
       CAST
     ========================= */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Nomor acuan laporan yang tampil ke pengguna.
     *
     * Formats: SRJ-2026-000123
     */
    public function getNomorReferensiAttribute(): string
    {
        return sprintf('SRJ-%s-%06d', $this->created_at?->year ?? now()->year, $this->id);
    }

    /**
     * Daftar status yang sah beserta label Bahasa Indonesia-nya.
     * Dipakai bersama oleh form admin, daftar laporan, statistik, dan
     * tampilan pelapor supaya warna + label konsisten di semua halaman.
     */
    public const STATUS = [
        'diterima' => 'Diterima',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    /**
     * Urutan alur status, daribaru ke selesai.
     */
    public const URUTAN_STATUS = ['diterima', 'diproses', 'selesai'];

    public function labelStatus(?string $status = null): string
    {
        $status ??= $this->latestStatus?->status;

        return self::STATUS[$status] ?? 'Menunggu';
    }


    /* =========================
       RELASI KE USER / PELAPOR
    ========================= */
    public function user()
    {
        // `withTrashed()`: laporan adalah data aduan publik. Kalau
        // akun pelapornya dinonaktifkan, nama pelapor tetap harus
        // tampil di detail laporan milik petugas -- bukan menjadi
        // "null" yang tidak bisa dijelaskan ke siapa pun.
        return $this->belongsTo(
            User::class,
            'user_id'
        )->withTrashed();
    }

    /* =========================
       RELASI KE HISTORY STATUS
    ========================= */
    public function statuses()
    {
        /*
         * Diurutkan `id_status`, bukan `created_at`.
         *
         * `latestStatus` dan `LaporanStats` memakai `MAX(id_status)`.
         * Kalau relasi ini tetap urut `created_at`, timeline bisa
         * menampilkan urutan yang berbeda dari badge status terbaru --
         * terakhir di timeline bukan yang terakhir di database, dan
         * `created_at` bisa seri karena presisi kolom MySQL datetime
         * hanya sampai detik.
         *
         * Urutan tetap terbaru-ke-terlama, sama seperti sebelumnya,
         * jadi kedua view yang memakainya tidak perlu diubah arah.
         */
        return $this->hasMany(
            TabelStatus::class,
            'report_id'
        )->latest('id_status');
    }

    /* =========================
       STATUS TERBARU
    ========================= */
    public function latestStatus()
    {
        // Aturan "status terbaru" di seluruh project ini adalah baris
        // `tabel_status` dengan `id_status` terbesar -- bukan
        // `created_at` terbesar.
        //
        // Alasannya `id_status` adalah primary key auto-increment, jadi
        // selalu urut dan tidak pernah seri. `created_at` bisa bernilai
        // sama untuk dua baris yang ditulis di detik yang sama (presisi
        // detik pada kolom datetime MySQL), dan kalau nanti ada data
        // lama yang diimpor dengan tanggal tidak wajar, urutannya bisa
        // tidak cocok dengan urutan di mana datanya benar-benar
        // ditambahkan.
        //
        // `LaporanStats` memakai aturan yang sama, jadi badge di kartu
        // dan angka pada filter tidak akan pernah saling bertentangan.
        return $this->hasOne(
            TabelStatus::class,
            'report_id'
        )->ofMany('id_status', 'max');
    }

    /* =========================
       ALIAS STATUS TERBARU
    ========================= */
    public function statusTerbaru()
    {
        return $this->latestStatus();
    }
}