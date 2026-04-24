<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Serojap - Pelaporan Jalan Rusak Purwakarta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="{{ asset('assets/pelapor/css/index.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            overflow-x: hidden;
            background-color: #ffffff;
        }

        /* Memberi jarak atas agar tidak nempel */
        .hero-section {
            padding-top: 6rem !important; 
            position: relative;
            z-index: 1;
        }

        /* Memperbaiki Gradasi Hijau agar muncul di latar belakang */
        .gradient-bg {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1; /* Di bawah konten */
            pointer-events: none;
        }

        /* Bingkai Foto (Card Style) seperti di awal */
        .img-frame {
            background-color: #ffffff;
            padding: 15px; /* Ini yang bikin efek bingkai putih */
            border-radius: 50px;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.15);
            border: 1px solid #f0f0f0;
            display: inline-block;
            width: 100%;
        }

        .img-hero {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: 40px; 
        }

        /* Styling Tombol */
        .btn-custom-action {
            background-color: #f8f9fa !important;
            color: #4b5563 !important;
            border: 1px solid #e5e7eb !important;
            transition: all 0.2s ease !important;
            padding: 1rem 2.5rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            width: 100%;
            text-align: center;
            text-decoration: none;
        }

        @media (min-width: 768px) {
            .btn-custom-action {
                width: auto;
            }
        }
        
        .btn-custom-action:active {
            background-color: #22c55e !important;
            color: white !important;
            border-color: #22c55e !important;
        }

        /* Titik Hijau */
        .dot-green {
            width: 10px;
            height: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>

<body class="bg-white">

    <main class="hero-section">
        <div class="gradient-bg"></div>

        <div class="container">
            <div class="row align-items-center gy-5">
                
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center bg-success bg-opacity-10 text-success px-4 py-2 border border-success border-opacity-25 rounded-pill mb-4">
                        <span class="dot-green"></span>
                        <small class="fw-bold text-uppercase tracking-wider" style="font-size: 10px;">Sistem Pelaporan Purwakarta</small>
                    </div>

                    <h1 class="display-3 fw-bold mb-3 text-dark" style="line-height: 1.2;">
                        Sistem Pelaporan <br>
                        <span class="text-success italic">Jalan Rusak</span> <br>
                        Purwakarta
                    </h1>

                    <p class="lead text-muted mb-5" style="font-size: 1.15rem; max-width: 500px;">
                        Satu platform terintegrasi untuk masyarakat melaporkan kerusakan jalan demi infrastruktur Purwakarta yang lebih baik.
                    </p>

                    <div class="d-flex flex-column flex-md-row gap-3">
                        <a href="{{ route('register') }}" class="btn btn-custom-action">
                            Register
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-custom-action">
                            Log In
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="img-frame">
                        <img src="{{ asset('assets/pelapor/images/jalan-purwakarta.png') }}" 
                             alt="Jalan Purwakarta" 
                             class="img-hero">
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="container py-5 mt-5 border-top border-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <p class="small text-muted mb-0">© 2026 Serojap Purwakarta</p>
            <p class="small text-muted mb-0">Indonesia University of Education Project</p>
        </div>
    </footer>

    <script src="{{ asset('assets/pelapor/js/index.js') }}" defer></script>
</body>
</html>