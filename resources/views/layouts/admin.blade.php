<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin — Serojap')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50/50 min-h-screen flex">

    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0 z-20">
        
        <div class="h-20 px-6 border-b border-slate-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 text-[#2657c1] rounded-lg flex items-center justify-center font-bold">
                <i class="mdi mdi-flower-tulip text-xl"></i>
            </div>
            <span class="font-black text-lg tracking-tight text-[#2657c1]">SEROJAP</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <span class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Menu Utama</span>
            
            <a href="{{ url('/admin/dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#2657c1] transition">
                <i class="mdi mdi-view-dashboard-outline text-lg"></i>
                Dashboard
            </a>

            <a href="#" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#2657c1] transition">
                <i class="mdi mdi-file-document-outline text-lg"></i>
                Manajemen Laporan
            </a>

            <a href="#" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#2657c1] transition">
                <i class="mdi mdi-help-circle-outline text-lg"></i>
                Manajemen FAQ
            </a>

            <a href="#" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-[#2657c1] transition">
                <i class="mdi mdi-chart-bar text-lg"></i>
                Grafik Statistik
            </a>
        </nav>

        <div class="mt-auto px-4 py-4 border-t border-slate-100 bg-slate-50/30">
            <a href="{{ route('admin.profile.index') }}" 
               class="flex items-center gap-2.5 p-2 rounded-xl transition group block hover:bg-white hover:shadow-sm border border-transparent">
                
                <div class="w-9 h-9 bg-gradient-to-br from-[#2657c1] to-[#226d71] rounded-xl flex items-center justify-center text-white font-black text-xl shadow-md shadow-blue-100 overflow-hidden flex-shrink-0">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-[#2657c1] transition">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                        Edit Profil
                    </p>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition uppercase tracking-wider cursor-pointer">
                    <i class="mdi mdi-logout text-sm"></i>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 pl-64 flex flex-col min-h-screen">
        
        <header class="h-20 px-8 bg-white border-b border-slate-200 flex items-center justify-end sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-[#2657c1] bg-blue-50 px-3 py-1.5 rounded-full uppercase tracking-widest">
                    {{ auth()->user()->role ?? 'ADMIN' }}
                </span>
            </div>
        </header>

        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

</body>
</html>