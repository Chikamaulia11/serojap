<<<<<<< HEAD
<<<<<<< HEAD
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', 'Public Sans', sans-serif; }
    </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
=======
>>>>>>> 43717ca (fix workspace clean)
=======
<!DOCTYPE html>
<html>
<head>
    <title>SEROJAP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
</head>

<body>

<div class="navbar">

    <div class="nav-left">
        <img src="{{ asset('logo.png') }}">
        <span>SEROJAP</span>
    </div>

    <!-- ✅ URUTAN FIX -->
    <div class="nav-menu">
        <a href="#dashboard" class="nav-item active">Dashboard</a>
        <a href="#prosedur" class="nav-item">Prosedur</a>
        <a href="#laporan" class="nav-item">Laporan</a>
        <a href="#riwayat" class="nav-item">Riwayat</a>
        <a href="#faq" class="nav-item">FAQ</a>
    </div>

    <div style="display:flex; align-items:center; gap:15px;">
        <div class="hamburger" onclick="toggleMenu()">☰</div>

        @if(auth()->check())
        <a href="{{ route('profile.edit') }}" class="nav-profile">
            <img src="{{ auth()->user()->foto_profil 
                ? asset('storage/'.auth()->user()->foto_profil) 
                : 'https://i.pravatar.cc/100' }}">
            <span>{{ auth()->user()->name }}</span>
        </a>
        @endif
    </div>

</div>

<!-- MOBILE -->
<div id="mobileMenu" class="mobile-menu">
    <a href="#dashboard">Dashboard</a>
    <a href="#prosedur">Prosedur</a>
    <a href="#laporan">Laporan</a>
    <a href="#riwayat">Riwayat</a>
    <a href="#faq">FAQ</a>
</div>

<div class="content">
    @yield('content')
</div>

<script src="{{ asset('js/navbar.js') }}"></script>

</body>
</html>
>>>>>>> fba5a8a (save progress dashboard pelapor)
