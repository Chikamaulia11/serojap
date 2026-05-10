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