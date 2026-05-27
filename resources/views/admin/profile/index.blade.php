@extends('layouts.admin')

@section('title', 'Profil Admin — Serojap')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Pengaturan Akun Admin</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola informasi data profil, unggah foto profil dari galeri, dan keamanan administrator Serojap Anda.</p>
    </div>

    <div class="grid grid-cols-1 gap-8">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <i class="mdi mdi-account-card-details-outline text-xl text-[#2657c1]"></i>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Informasi Profil & Foto</h3>
                    <p class="text-xs text-slate-400">Perbarui nama, email, dan unggah foto profil resmi administrator Anda.</p>
                </div>
            </div>
            
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('patch')

                @if (session('status') === 'profile-updated')
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold rounded-xl flex items-center gap-2">
                        <i class="mdi mdi-check-circle-outline text-base text-emerald-600"></i>
                        Data profil dan foto administrator berhasil diperbarui!
                    </div>
                @endif

                @if ($errors->any() && ! $errors->updatePassword->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 text-xs font-semibold rounded-xl">
                        <ul class="list-disc pl-5 space-y-1 text-red-600 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                    
                    <div class="flex flex-col items-center gap-3 flex-shrink-0 bg-slate-50 p-6 rounded-2xl border border-slate-100 w-full md:w-auto">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Foto Profil</span>
                        
                        <div class="w-28 h-28 bg-gradient-to-br from-[#2657c1] to-[#226d71] rounded-2xl flex items-center justify-center text-white font-black text-4xl shadow-md shadow-blue-100 overflow-hidden relative" id="avatar-container">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" id="avatar-preview" class="w-full h-full object-cover">
                            @else
                                <div id="avatar-text">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                                <img id="avatar-preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                        
                        <button type="button" onclick="document.getElementById('profile_image').click()"
                            class="px-3 py-1.5 bg-white border border-slate-200 hover:border-blue-400 text-slate-600 hover:text-blue-600 text-[11px] font-bold rounded-lg shadow-sm transition flex items-center gap-1 cursor-pointer">
                            <i class="mdi mdi-camera-outline text-sm"></i>
                            Pilih Gambar Galeri
                        </button>
                    </div>

                    <div class="flex-1 w-full grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-[#2657c1] focus:ring-2 focus:ring-blue-50 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-[#2657c1] focus:ring-2 focus:ring-blue-50 outline-none transition">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                        class="px-5 py-2.5 bg-[#2657c1] hover:bg-[#1d4496] text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                        <i class="mdi mdi-content-save text-base"></i>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <i class="mdi mdi-lock-outline text-xl text-amber-500"></i>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Ganti Kata Sandi</h3>
                    <p class="text-xs text-slate-400">Amankan akun administrator dengan memperbarui password secara berkala.</p>
                </div>
            </div>
            
            <form action="{{ route('password.update') }}" method="POST" class="p-8">
                @csrf
                @method('put')

                @if (session('status') === 'password-updated')
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold rounded-xl flex items-center gap-2">
                        <i class="mdi mdi-check-circle-outline text-base text-emerald-600"></i>
                        Kata sandi administrator Anda berhasil diperbarui!
                    </div>
                @endif

                @if ($errors->updatePassword->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 text-xs font-semibold rounded-xl">
                        <ul class="list-disc pl-5 space-y-1 text-red-600 font-medium">
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" required autocomplete="current-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-[#2657c1] focus:ring-2 focus:ring-blue-50 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kata Sandi Baru</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-[#2657c1] focus:ring-2 focus:ring-blue-50 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-[#2657c1] focus:ring-2 focus:ring-blue-50 outline-none transition">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                        class="px-5 py-2.5 bg-[#2657c1] hover:bg-[#1d4496] text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                        <i class="mdi mdi-content-save-check text-base"></i>
                        Simpan Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = document.getElementById('avatar-preview');
            const textPlaceholder = document.getElementById('avatar-text');

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (textPlaceholder) {
                    textPlaceholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection