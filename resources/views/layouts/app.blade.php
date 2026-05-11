<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SEROJAP</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    <style>
        body {
            font-family: 'Inter', 'Public Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7fb;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">

        <div class="nav-left">
            <img src="{{ asset('logo.png') }}">
            <span>SEROJAP</span>
        </div>

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
                        ? asset('storage/' . auth()->user()->foto_profil)
                        : 'https://i.pravatar.cc/100' }}">

                    <span>{{ auth()->user()->name }}</span>

                </a>
            @endif

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu" class="mobile-menu">
        <a href="#dashboard">Dashboard</a>
        <a href="#prosedur">Prosedur</a>
        <a href="#laporan">Laporan</a>
        <a href="#riwayat">Riwayat</a>
        <a href="#faq">FAQ</a>
    </div>

    <!-- CONTENT -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Navbar JS -->
    <script src="{{ asset('js/navbar.js') }}"></script>

</body>

</html>