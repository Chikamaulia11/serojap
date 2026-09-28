<?php

namespace App\Support;

/**
 * Peta warna, ikon, dan label status laporan.
 *
 * Warna status dulu ditulis ulang di tiap view: `diproses` biru di
 * daftar laporan tapi amber di halaman update status, `selesai` ungu
 * di satu halaman dan hijau di halaman lain. Semua view sekarang
 * membaca dari sini sehingga satu status selalu tampil sama.
 *
 * Kelas yang dikembalikan BUKAN utilitas Tailwind, melainkan kelas
 * dari `resources/css/status.css` (`.status-badge`, `.status-text`,
 * dan seterusnya) digabung dengan penanda per status (`.st-diterima`
 * dan sejenisnya). Alasannya nilai warna diturunkan dari `--diterima`,
 * `--diproses`, `--selesai`, `--ditolak` di `theme.css`, sehingga:
 *
 * - Warnanya sama persis dengan yang dipakai badge status di halaman
 *   lain yang menulis CSS sendiri, dan
 * - Tidak ikut berubah saat pengguna mengganti aksen maupun
 *   light/dark, karena status itu warna fungsional.
 *
 * Kalau dulu ditulis `bg-blue-100 text-blue-800 ring-blue-600/20`,
 * palet Tailwind tidak sama dengan palet status yang disepakati, dan
 * `bg-blue-100` yang terang membuat badge tidak terbaca di mode dark.
 *
 * Setiap status juga punya ikon sendiri supaya informasi tidak hanya
 * bergantung pada warna.
 */
class StatusPeta
{
    /**
     * Path ikon SVG (heroicons outline, 24px).
     */
    public const IKON = [
        'diterima' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'diproses' => 'M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z',
        'selesai' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'ditolak' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'default' => 'M12 8v4l3 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z',
    ];

    /**
     * @return array{key:string, label:string, badge:string, text:string, dot:string, ring:string, bg:string, ikon:string}
     */
    public static function get(?string $status): array
    {
        $key = isset(self::IKON[$status]) ? $status : 'default';

        $label = [
            'diterima' => 'Diterima',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'default' => 'Menunggu',
        ];

        // Penanda per status, dipakai bersama oleh kelima kunci gaya di
        // bawah supaya tiap status hanya ditulis sekali.
        $st = 'st-' . $key;

        return [
            'key' => $key,
            'ikon' => self::IKON[$key],
            'label' => $label[$key],
            'badge' => "status-badge {$st}",
            'text' => "status-text {$st}",
            'dot' => "status-dot {$st}",
            'ring' => "status-ring {$st}",
            'bg' => "status-bg {$st}",
        ];
    }

    /**
     * Opsi status untuk dropdown, berurutan alur kerja.
     *
     * @return array<string, string>
     */
    public static function opsiDropdown(): array
    {
        $opsi = [];

        foreach (['diterima', 'diproses', 'selesai', 'ditolak'] as $status) {
            $opsi[$status] = self::get($status)['label'];
        }

        return $opsi;
    }

    /**
     * Urutan alur normal, dipakai untuk timeline progres.
     */
    public static function alur(): array
    {
        return ['diterima', 'diproses', 'selesai'];
    }

    /**
     * Status berikutnya yang wajar setelah status sekarang.
     *
     * Petunjuk urutan, bukan penguncian: petugas tetap boleh mundur
     * atau menolak di tahap mana pun.
     */
    public static function berikutnya(?string $status): ?string
    {
        return match ($status) {
            'diterima' => 'diproses',
            'diproses' => 'selesai',
            default => null,
        };
    }
}
