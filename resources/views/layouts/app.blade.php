<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEROJAP</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 30px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
            color: #1f4674;
        }

        .nav-menu {
            display: flex;
            gap: 25px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }

        .nav-menu a:hover {
            color: #226d71;
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

        .content {
            padding: 30px;
        }

        .hero {
            background: linear-gradient(135deg, #1f4674, #226d71);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 20px;
        }

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
            margin: 0;
            color: #1f4674;
        }

        .card p {
            font-size: 20px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<div class="navbar">

    <div class="nav-left">
        <img src="{{ asset('logo.png') }}">
        <span>SEROJAP</span>
    </div>

    <div class="nav-menu">
        <a href="/dashboard">Dashboard</a>
        <a href="/report">Laporan</a>
        <a href="/my-report">Riwayat</a>
    </div>

    <div class="nav-right">
        <img src="{{ isset($authUser) && $authUser->foto_profil 
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