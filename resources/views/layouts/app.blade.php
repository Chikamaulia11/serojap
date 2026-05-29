<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SEROJAP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <div class="navbar">

        <a href="{{ route('dashboard') }}#dashboard" class="nav-left">
            <img src="{{ asset('logo.png') }}">
            <span>SEROJAP</span>
        </a>

        <div class="nav-menu">
            <a href="{{ route('dashboard') }}#dashboard" class="nav-item">Dashboard</a>
            <a href="{{ route('dashboard') }}#prosedur" class="nav-item">Prosedur</a>
            <a href="{{ route('dashboard') }}#laporan" class="nav-item">Laporan</a>
            <a href="{{ route('dashboard') }}#riwayat" class="nav-item">Riwayat</a>
            <a href="{{ route('dashboard') }}#faq" class="nav-item">FAQ</a>
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

    <div id="mobileMenu" class="mobile-menu">
        <a href="{{ route('dashboard') }}#dashboard">Dashboard</a>
        <a href="{{ route('dashboard') }}#prosedur">Prosedur</a>
        <a href="{{ route('dashboard') }}#laporan">Laporan</a>
        <a href="{{ route('dashboard') }}#riwayat">Riwayat</a>
        <a href="{{ route('dashboard') }}#faq">FAQ</a>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <footer class="footer-section" style="background-color: #ffffff; color: #1e293b; padding: 60px 40px 40px; margin-top: 60px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; border-top-left-radius: 20px; border-top-right-radius: 20px; width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; box-sizing: border-box;">
        <div style="width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 20px; box-sizing: border-box;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 50px;">
                <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}" alt="Logo SEROJAP" style="height: 45px; width: auto; object-fit: contain;">
                <div>
                    <h3 style="font-size: 22px; font-weight: 700; margin: 0; letter-spacing: 0.5px; color: #1e293b;">SEROJAP</h3>
                    <p style="font-size: 13px; opacity: 0.8; margin: 0; color: #1e293b;">Sistem Pelaporan Jalan Purwakarta</p>
                </div>
            </div>

            <div style="margin-top: 50px; border-top: 1px solid rgba(30, 41, 59, 0.15); padding-top: 25px; text-align: center; font-size: 13px; opacity: 0.8; color: #1e293b;">
                <p style="margin: 0;">© {{ date('Y') }} SEROJAP - Sistem Pelaporan Jalan Purwakarta</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/navbar.js') }}"></script>

</body>

</html>