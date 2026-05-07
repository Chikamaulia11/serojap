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

        /* Dibuat lebih naik (padding dikurangi dari 6rem ke 3rem) */
        .hero-section {
            padding-top: 3rem !important; 
            padding-bottom: 2rem !important;
            position: relative;
            z-index: 1;
            min-height: 85vh; /* Memastikan konten tetap proposional */
            display: flex;
            align-items: center;
        }

        .gradient-bg {
            position: absolute;
            top: -100px;
            left: -100px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34, 109, 113, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .img-frame {
            background-color: #ffffff;
            padding: 15px;
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

        .btn-custom-action {
            background-color: #f1f5f5 !important;
            color: #4b5563 !important;
            border: 1px solid #d1dbdb !important;
            transition: all 0.2s ease-in-out !important;
            padding: 1rem 2.5rem !important;
            font-weight: 700 !important;
            border-radius: 50px !important;
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        @media (min-width: 768px) {
            .btn-custom-action {
                width: auto;
            }
        }
        
        .btn-custom-action:hover {
            background-color: #e2eaea !important;
            border-color: #226d71 !important;
            color: #226d71 !important;
        }

        .btn-custom-action:active {
            background-color: #226d71 !important;
            color: #ffffff !important;
            border-color: #226d71 !important;
            transform: scale(0.96);
        }

        .dot-green {
            width: 10px;
            height: 10px;
            background-color: #226d71;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .text-teal {
            color: #226d71 !important;
        }

        .bg-teal-light {
            background-color: rgba(34, 109, 113, 0.1) !important;
        }
    </style>
</head>

<body class="bg-white">

    <main class="hero-section">
        <div class="gradient-bg"></div>

        <div class="container">
            <div class="row align-items-center gy-5">
                
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center bg-teal-light text-teal px-4 py-2 border border-teal-light border-opacity-25 rounded-pill mb-4">
                        <span class="dot-green"></span>
                        <small class="fw-bold text-uppercase tracking-wider" style="font-size: 10px;">Sistem Pelaporan Purwakarta</small>
                    </div>

                    <h1 class="display-3 fw-bold mb-3 text-dark" style="line-height: 1.2;">
                        Sistem Pelaporan <br>
                        <span class="text-teal italic">Jalan Rusak</span> <br>
                        Purwakarta
                    </h1>

                    <p class="lead text-muted mb-4" style="font-size: 1.15rem; max-width: 500px;">
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

    <footer class="container py-4 border-top border-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <p class="small text-muted mb-0">© 2026 Serojap Purwakarta</p>
            <p class="small text-muted mb-0">Indonesia University of Education Project</p>
        </div>
    </footer>

    <script src="{{ asset('assets/pelapor/js/index.js') }}" defer></script>
</body>
</html>