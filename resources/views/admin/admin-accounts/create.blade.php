@extends('layouts.admin')

@section('title', 'Manajemen Akun Admin — SEROJAP')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.admin-accounts.index') }}" class="text-[#2657c1] text-sm hover:underline flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Manajemen Admin
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-3">Tambah Admin</h1>
        <p class="text-gray-500 text-sm">Super admin dapat menambahkan akun admin baru.</p>
    </div>


    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.admin-accounts.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                        class="w-full bg-gray-50 border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('nama')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('email')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full bg-gray-50 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('password')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-50 border @error('password_confirmation') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('password_confirmation')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Posisi</label>
                    <input type="text" name="posisi" value="{{ old('posisi') }}"
                        class="w-full bg-gray-50 border @error('posisi') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                    @error('posisi')
                        <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-[#2657c1] hover:bg-[#1f4674] text-white font-bold rounded-lg px-4 py-3 transition shadow-md shadow-blue-500/20 active:scale-[0.98]">
                    Tambah Admin
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

