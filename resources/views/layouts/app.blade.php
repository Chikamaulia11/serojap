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

    <style>
        :root {
            --primary: #1f4674;
            --secondary: #226d71;
            --bg: #f5f7fa;
            --white: #ffffff;
            --text: #333;
        }

        * { box-sizing: border-box; margin: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
        }

        /* ================= NAVBAR ================= */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            background: var(--white);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-left img {
            width: 35px;
        }

        .nav-left span {
            font-weight: 600;
            color: var(--primary);
        }

        .nav-menu {
            display: flex;
            gap: 20px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #555;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-menu a:hover {
            color: var(--secondary);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-right img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-right span {
            font-size: 14px;
        }

        /* ================= CONTENT ================= */
        .content {
            padding: 30px;
        }

        /* ================= HERO ================= */
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        /* ================= CARDS ================= */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
            gap: 15px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .card h3 {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background: var(--secondary);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="navbar">

    <!-- LEFT -->
    <div class="nav-left">
        <img src="{{ asset('logo.png') }}">
        <span>SEROJAP</span>
    </div>

    <!-- MENU -->
    <div class="nav-menu">
        <a href="/dashboard">Dashboard</a>
        <a href="/report">Laporan Aduan</a>
        <a href="/faq">FAQ</a>
        <a href="/prosedur">Prosedur</a>
        <a href="/my-report">Riwayat</a>
    </div>

    <!-- USER -->
    <div class="nav-right">
        @if(auth()->check())

            <img src="{{ auth()->user()->foto_profil 
                ? asset('storage/'.auth()->user()->foto_profil) 
                : 'https://i.pravatar.cc/100' }}">

            <span>{{ auth()->user()->name }}</span>

        @else

            <img src="https://i.pravatar.cc/100">
            <span>Guest</span>

        @endif
    </div>

</div>

<div class="content">
    @yield('content')
</div>

</body>
</html>
>>>>>>> fba5a8a (save progress dashboard pelapor)
