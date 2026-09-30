<!DOCTYPE html>
{{--
    `data-mode-locked="light"` dipasang di sini, di tag `<html>`,
    SEBUMUM CSS dimuat -- bukan lewat JavaScript.

    Alasan atributnya ada di server: seluruh sistem tema dibaca dari
    `data-mode-resolved`, sedangkan `theme.js` baru jalan setelah
    `DOMContentLoaded`. Kalau kuncinya baru dipasang JavaScript,
    halaman auth sempat ter-render memakai mode gelap yang tersimpan
    lalu berubah -- kedip yang persis seperti yang dihindari script
    anti-FOUC.

    Yang dikunci HANYA `data-mode-resolved`. `data-mode` dan
    `localStorage` tetap menyimpan pilihan asli, jadi pengunjung yang
    memilih dark mode tidak kehilangan preferensinya: dia hanya
    tertahan di light selama berada di layar auth, dan masuk ke
    aplikasi dalam dark mode begitu login. Tidak ada yang ditulis
    ulang ke `localStorage`.

    `theme.js` menghormati kunci ini di `resolveMode()` dan
    `theme-bootstrap` membacanya juga, jadi keduanya tidak saling
    menimpa. `tools/theme-audit-inject.js` ikut menghormatinya:
    halaman terkunci diukur sebagai light pada SEMUA mode.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-mode-locked="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('partials.theme-bootstrap')

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">

                {{-- Picker tema, di baris yang sama dengan logo. Halaman auth
                     tidak punya navbar, jadi ini titik paling dekat dengan
                     "header" yang tersedia.

                     `modeLocked` diteruskan ke partial: di halaman auth
                     mode terkunci light, jadi tombol Light/Dark/Sistem
                     akan berbohong -- memilih "Dark" tidak mengubah apa
                     pun. Swatch aksen tetap ada, jadi warna aksennya
                     masih bisa diganti di sini. --}}
            <div class="w-full sm:max-w-md px-6 flex items-center justify-between">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>

                @include('partials.theme-picker', ['variant' => 'guest', 'modeLocked' => true])
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

    </body>
</html>
