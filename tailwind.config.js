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
             * primary = teal brand SEROJAP
             * accent  = biru untuk area admin/super admin
             */
            colors: {
                primary: {
                    50: '#f0fafa',
                    100: '#d5f2f1',
                    200: '#abe5e4',
                    300: '#74d2d1',
                    400: '#3fb5b5',
                    500: '#24999a',
                    600: '#226d71',
                    700: '#1d5a5d',
                    800: '#1a4a4c',
                    900: '#183e3f',
                    950: '#0a2526',
                },
                accent: {
                    50: '#eef3fd',
                    100: '#dae5fa',
                    200: '#bdd1f4',
                    300: '#92b3ec',
                    400: '#628ce0',
                    500: '#3f6ad2',
                    600: '#2657c1',
                    700: '#1f4674',
                    800: '#1d3b61',
                    900: '#1d3351',
                    950: '#142139',
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
