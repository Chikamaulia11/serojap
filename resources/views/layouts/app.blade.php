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

     <!-- ================= FOOTER ================= -->
   <footer class="footer-section" style="background-color: #ffffff; color: #1e293b; padding: 60px 40px 40px; margin-top: 60px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; border-top-left-radius: 20px; border-top-right-radius: 20px; width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; box-sizing: border-box;">
        <div style="width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 20px; box-sizing: border-box;">
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 50px;">
                <img src="{{ asset('assets/pelapor/images/logo-serojap.png') }}" alt="Logo SEROJAP" style="height: 45px; width: auto; object-fit: contain;">
                <div>
                    <h3 style="font-size: 22px; font-weight: 700; margin: 0; letter-spacing: 0.5px; color: #1e293b;">SEROJAP</h3>
                    <p style="font-size: 13px; opacity: 0.8; margin: 0; color: #1e293b;">Sistem Pelaporan Jalan Purwakarta</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: row; flex-wrap: nowrap; justify-content: space-between; align-items: flex-start; gap: 20px; width: 100%;">
                
                <div style="flex: 0 0 33%; padding-right: 20px; border-right: 1px solid rgba(30, 41, 59, 0.2); min-height: 100px; box-sizing: border-box;">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <span style="font-size: 18px; line-height: 1; padding-top: 2px;">📍</span>
                        <div>
                            <h5 style="font-size: 16px; font-weight: 600; margin: 0 0 10px 0; letter-spacing: 0.3px; color: #1e293b;">Gedung Baru</h5>
                            <p style="font-size: 14px; opacity: 0.9; line-height: 1.6; margin: 0; color: #1e293b;">
                                Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Purwakarta
                            </p>
                        </div>
                    </div>
                </div>

                <div style="flex: 0 0 20%; padding-right: 20px; border-right: 1px solid rgba(30, 41, 59, 0.2); min-height: 100px; box-sizing: border-box;">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <span style="font-size: 18px; line-height: 1; padding-top: 2px;">✉</span>
                        <div>
                            <h5 style="font-size: 16px; font-weight: 600; margin: 0 0 10px 0; letter-spacing: 0.3px; color: #1e293b;">Surel</h5>
                            <p style="font-size: 14px; margin: 0;">
                                <a href="mailto:info@jabarprov.go.id" style="color: #1e293b; text-decoration: none; opacity: 0.9;">info@serojap.purwakartakab.go.id</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div style="flex: 0 0 22%; padding-right: 20px; border-right: 1px solid rgba(30, 41, 59, 0.2); min-height: 100px; box-sizing: border-box;">
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <span style="font-size: 18px; line-height: 1; padding-top: 2px;">📋</span>
                        <div>
                            <h5 style="font-size: 16px; font-weight: 600; margin: 0 0 10px 0; letter-spacing: 0.3px; color: #1e293b;">Umpan Balik</h5>
                            <p style="font-size: 14px; margin: 0;">
                                <a href="#" style="color: #1e293b; text-decoration: none; opacity: 0.9; line-height: 1.5; display: block;">Isi survei performa situs web</a>
                            </p>
                        </div>
                    </div>
                </div>

                <div style="flex: 0 0 25%; box-sizing: border-box;">
                    <div style="display: flex; gap: 15px; align-items: flex-start; flex-direction: column; padding-left: 10px;">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span style="font-size: 18px; line-height: 1;">🌐</span>
                            <h5 style="font-size: 16px; font-weight: 600; margin: 0; letter-spacing: 0.3px; color: #1e293b;">Media Sosial</h5>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 5px; flex-wrap: nowrap;">
                            <!-- Facebook -->
                                <a href="#" style="width: 38px; height: 38px; border: 1px solid rgba(30, 41, 59, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1e293b; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='rgba(30, 41, 59, 0.05)'" onmouseout="this.style.background='none'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1e293b" width="16" height="16">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>

                            <!-- Instagram -->
                                <a href="#" style="width: 38px; height: 38px; border: 1px solid rgba(30, 41, 59, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1e293b; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='rgba(30, 41, 59, 0.05)'" onmouseout="this.style.background='none'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1e293b" width="16" height="16">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                                    </svg>
                                </a>

                            <!-- Twitter -->
                                <a href="#" style="width: 38px; height: 38px; border: 1px solid rgba(30, 41, 59, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1e293b; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='rgba(30, 41, 59, 0.05)'" onmouseout="this.style.background='none'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1e293b" width="14" height="14">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>

                            <!-- YouTube -->
                                <a href="#" style="width: 38px; height: 38px; border: 1px solid rgba(30, 41, 59, 0.3); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1e293b; text-decoration: none; transition: background 0.3s;" onmouseover="this.style.background='rgba(30, 41, 59, 0.05)'" onmouseout="this.style.background='none'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#1e293b" width="16" height="16">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93 .502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                        </div>
                    </div>
                </div>

            </div>

            <div style="margin-top: 50px; border-top: 1px solid rgba(30, 41, 59, 0.15); padding-top: 25px; text-align: center; font-size: 13px; opacity: 0.8; color: #1e293b;">
                <p style="margin: 0;">© {{ date('Y') }} SEROJAP - Sistem Pelaporan Jalan Purwakarta</p>
            </div>

        </div>
    </footer>

    <!-- Navbar JS -->
    <script src="{{ asset('js/navbar.js') }}"></script>

</body>

</html>