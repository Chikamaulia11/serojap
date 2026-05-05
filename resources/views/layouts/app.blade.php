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
        <img src="{{ $authUser && $authUser->foto_profil 
            ? asset('storage/'.$authUser->foto_profil) 
            : 'https://i.pravatar.cc/100' }}">
        <span>{{ $authUser->name ?? 'Guest' }}</span>
    </div>

</div>

<div class="content">
    @yield('content')
</div>

</body>
</html>