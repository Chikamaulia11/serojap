@extends('layouts.superadmin')

@section('title', 'Dashboard Super Admin — SEROJAP')

@section('content')

<div class="max-w-7xl mx-auto space-y-8">

    <!-- Header -->
    <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#1f4674] via-[#2657c1] to-[#226d71] p-8 shadow-lg shadow-blue-900/10">

        <div class="absolute -top-20 -right-16 w-72 h-72 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-24 right-28 w-56 h-56 rounded-full bg-white/5"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 text-white/90 text-xs font-bold mb-4">
                    <i class="mdi mdi-shield-crown-outline text-base"></i>
                    Super Admin Panel
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                    Dashboard Super Admin
                </h1>

                <p class="text-sm md:text-base text-blue-50/90 mt-3 max-w-2xl leading-relaxed">
                    Panel utama untuk mengelola akun admin dan pelapor pada sistem SEROJAP.
                    Admin dan super admin dibuat melalui panel ini, sedangkan pelapor tetap dapat registrasi sendiri.
                </p>
            </div>

            <a href="{{ route('superadmin.accounts.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-[#2657c1] text-sm font-extrabold hover:bg-blue-50 transition shadow-lg shadow-black/10">
                <i class="mdi mdi-account-cog-outline text-lg"></i>
                Buka Manajemen Akun
            </a>

        </div>

    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white border border-slate-200 rounded-[24px] p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-13 h-13 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center">
                    <i class="mdi mdi-account-tie-outline text-3xl"></i>
                </div>

                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">
                    ADMIN
                </span>
            </div>

            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-5">
                Kelola Admin
            </p>

            <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                Akun Admin
            </h2>

            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Tambah admin baru, ubah data profil, reset password, dan hapus akun admin yang tidak digunakan.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="mdi mdi-account-group-outline text-3xl"></i>
                </div>

                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold">
                    PELAPOR
                </span>
            </div>

            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-5">
                Kelola Pelapor
            </p>

            <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                Akun Pelapor
            </h2>

            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Pantau akun pelapor yang terdaftar, bantu ubah data akun, atau hapus akun jika diperlukan.
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-[24px] p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-start justify-between gap-4">
                <div class="w-13 h-13 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="mdi mdi-shield-check-outline text-3xl"></i>
                </div>

                <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold">
                    AKSES UTAMA
                </span>
            </div>

            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-5">
                Kontrol Sistem
            </p>

            <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                Super Admin
            </h2>

            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Area khusus untuk kontrol akun sistem yang dipisahkan dari dashboard admin biasa.
            </p>
        </div>

    </div>

    <!-- Action Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <div class="lg:col-span-3 bg-white border border-slate-200 rounded-[24px] p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#2657c1] flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-account-cog-outline text-2xl"></i>
                </div>

                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Manajemen Akun Terpusat
                    </h2>

                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Gunakan halaman manajemen akun untuk membuat akun admin baru, mengelola pelapor,
                        mengubah password, serta menjaga data pengguna tetap rapi dan terkontrol.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('superadmin.accounts.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#2657c1] text-white text-sm font-bold hover:bg-[#1f4674] transition shadow-md shadow-blue-500/20">
                            <i class="mdi mdi-format-list-bulleted"></i>
                            Lihat Daftar Akun
                        </a>

                        <a href="{{ route('superadmin.accounts.create') }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                            <i class="mdi mdi-account-plus-outline"></i>
                            Tambah Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gradient-to-br from-slate-900 to-slate-700 rounded-[24px] p-6 shadow-sm text-white">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mb-5">
                <i class="mdi mdi-information-outline text-2xl"></i>
            </div>

            <h2 class="text-lg font-extrabold">
                Catatan Akses
            </h2>

            <p class="text-sm text-slate-200 mt-2 leading-relaxed">
                Admin dan super admin tidak registrasi sendiri. Pembuatan akun admin dilakukan dari panel super admin.
                Pelapor tetap bisa mendaftar mandiri lewat halaman register.
            </p>
        </div>

    </div>

</div>

@endsection