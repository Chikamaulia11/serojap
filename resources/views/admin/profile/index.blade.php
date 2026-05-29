@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Profil Admin
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Kelola informasi profil admin yang sedang login.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-100 flex items-center gap-4">

            <div class="w-20 h-20 rounded-full overflow-hidden bg-gradient-to-br from-[#2657c1] to-[#226d71] flex items-center justify-center text-white text-2xl font-bold">

                @if($admin->foto_profil)
                    <img src="{{ asset('storage/' . $admin->foto_profil) }}"
                         alt="Foto Profil"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                @endif

            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    {{ $admin->name }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ $admin->email }}
                </p>

                <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                </span>
            </div>

        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nama
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $admin->name) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Email
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $admin->email) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Foto Profil
                </label>
                <input type="file"
                       name="foto_profil"
                       accept="image/png,image/jpeg,image/jpg"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm bg-white">
                <p class="text-xs text-slate-400 mt-1">
                    Format: JPG, JPEG, PNG. Maksimal 2MB.
                </p>
            </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-700">
                            Ubah Password
                        </h3>

                        <p class="text-xs text-slate-400 mt-0.5">
                            Kosongkan jika tidak ingin mengganti password.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Password Baru
                        </label>

                        <input type="password"
                               name="password"
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Konfirmasi Password Baru
                        </label>

                        <input type="password"
                               name="password_confirmation"
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                    Kembali
                </a>

                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium bg-[#2657c1] text-white hover:bg-blue-700 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>

@endsection