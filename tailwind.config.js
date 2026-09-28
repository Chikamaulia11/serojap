import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',

        /*
         * `app/Support/StatusPeta.php` hanya memuat nama kelas Tailwind
         * literal (mis. `bg-blue-100`, `ring-amber-600/20`) untuk
         * warna badge status, dan kelas-kelas itu dipakai lewat
         * komponen `x-status-badge` serta timeline.
         *
         * Tanpa baris di bawah ini, Tailwind tidak akan pernah memindai
         * file PHP itu: hasilnya `x-status-badge` dirender tanpa
         * background, ikon, atau warna teks sama sekali -- badge
         * "Selesai" dan "Ditolak" jadi tidak bisa dibedakan. Nama
         * kelasnya ditulis utuh di PHP, jadi memindai file aslinya sudah
         * cukup, tanpa perlu daftar manual.
         */
        './app/Support/StatusPeta.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                /*
                 * Figtree DIHAPUS dari sini. Tailwind menyertakan
                 * Figtree sebagai font default scaffolding Breeze, tapi
                 * tidak satu pun layout memuat Figtree dari Google
                 * Fonts -- semuanya hanya memuat Inter, Roboto, Poppins,
                 * dan Public Sans. Akibatnya setiap `font-sans` di
                 * project ini diam-diam jatuh ke font bawaan browser,
                 * sehingga teks form/button terlihat beda dari teks
                 * navigasi yang mewarisi `body`.
                 *
                 * Sekarang `font-sans` = font yang benar-benar dimuat.
                 */
                sans: ['Inter', 'Public Sans', ...defaultTheme.fontFamily.sans],
            },

            /*
             * Palet brand.
             *
             * Sebelumnya tiap halaman auth menulis warna hex sendiri:
             * #226d71, #227474 (typo), #14b8a6, #2657c1. Palet ini
             * jadi satu sumber, dan kelas `primary-*` yang dipakai
             * `admin/laporan/index.blade.php` akhirnya benar-benar
             * punya CSS -- sebelumnya tidak ada palet primary sama
             * sekali sehingga tombolnya transparent.
             *
             * Sekarang semua nama warna brand membaca dari tiga
             * variable di `resources/css/theme.css`, bukan hex.
             *
             * Kenapa ramp-nya sengaja dibuat datar: draft tema yang
             * disetujui hanya menyediakan tiga nilai per aksen
             * (`--accent`, `--accent-deep`, `--accent-tint`). Kalau
             * kita mengarang stop warna di antaranya, kita keluar
             * dari palet yang sudah direview. Karena itu setiap stop
             * dipetakan ke salah satu dari tiga nilai itu.
             *
             * Konsekuensi yang perlu diketahui: `bg-primary-200`
             * dan `bg-primary-300` menghasilkan warna yang SAMA.
             * Jangan memakai angka berbeda dengan maksud "lebih
             * gelap" atau "lebih terang" -- pakai `brand-deep` dan
             * `brand-tint` yang namanya memang jujur.
             *
             * `primary` dan `accent` masih dipertahankan karena view
             * yang sudah ada memakainya. Keduanya kini menunjuk ke
             * sistem yang sama, jadi tidak ada lagi dua warna brand
             * yang berbeda dalam satu aplikasi.
             */
            colors: {
                brand: {
                    tint: 'var(--accent-tint)',
                    DEFAULT: 'var(--accent)',
                    deep: 'var(--accent-deep)',
                },
                primary: {
                    50: 'var(--accent-tint)',
                    100: 'var(--accent-tint)',
                    200: 'var(--accent-tint)',
                    300: 'var(--accent-tint)',
                    400: 'var(--accent)',
                    500: 'var(--accent)',
                    600: 'var(--accent)',
                    700: 'var(--accent-deep)',
                    800: 'var(--accent-deep)',
                    900: 'var(--accent-deep)',
                },
                accent: {
                    50: 'var(--accent-tint)',
                    100: 'var(--accent-tint)',
                    200: 'var(--accent-tint)',
                    300: 'var(--accent)',
                    400: 'var(--accent)',
                    500: 'var(--accent)',
                    600: 'var(--accent)',
                    700: 'var(--accent-deep)',
                    800: 'var(--accent-deep)',
                    900: 'var(--accent-deep)',
                },
            },

            borderRadius: {
                card: '0.875rem',
            },

            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },

            animation: {
                'fade-in-up': 'fade-in-up 200ms ease-out',
            },
        },
    },

    plugins: [forms],
};
