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
 * Warna teks dipilih kontrasnya minimal 4.5:1 terhadap latar putih
 * (WCAG AA), dan setiap status juga punya ikon supaya informasi tidak
 * hanya bergantung pada warna.
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

        $peta = [
            'diterima' => [
                'label' => 'Diterima',
                'badge' => 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-600/20',
                'text' => 'text-blue-700',
                'dot' => 'bg-blue-600',
                'ring' => 'ring-blue-500/30',
                'bg' => 'bg-blue-50',
            ],
            'diproses' => [
                'label' => 'Diproses',
                'badge' => 'bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/20',
                'text' => 'text-amber-700',
                'dot' => 'bg-amber-600',
                'ring' => 'ring-amber-500/30',
                'bg' => 'bg-amber-50',
            ],
            'selesai' => [
                'label' => 'Selesai',
                'badge' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20',
                'text' => 'text-emerald-700',
                'dot' => 'bg-emerald-600',
                'ring' => 'ring-emerald-500/30',
                'bg' => 'bg-emerald-50',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'badge' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-600/20',
                'text' => 'text-rose-700',
                'dot' => 'bg-rose-600',
                'ring' => 'ring-rose-500/30',
                'bg' => 'bg-rose-50',
            ],
            'default' => [
                'label' => 'Menunggu',
                'badge' => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-500/20',
                'text' => 'text-slate-600',
                'dot' => 'bg-slate-500',
                'ring' => 'ring-slate-400/30',
                'bg' => 'bg-slate-50',
            ],
        ];

        return ['key' => $key, 'ikon' => self::IKON[$key]] + $peta[$key];
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
