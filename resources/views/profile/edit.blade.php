<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"> {{ __('Pengaturan Profil') }} </h2>
    </x-slot>

    <style> [x-cloak] { display: none !important; } </style>

    <div class="py-12 bg-gray-50" x-data="{ openSettings: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">

                <div class="md:col-span-4 flex flex-col space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden flex-grow flex flex-col">
                        <div class="p-8 text-center flex-grow flex flex-col justify-center">

                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')

                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">

                                <div class="relative inline-block mb-6">
                                    <img id="preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" class="h-32 w-32 object-cover rounded-full border-4 border-white shadow-md mx-auto">
                                    <label for="avatar" class="absolute bottom-0 right-0 bg-indigo-600 p-2 rounded-full cursor-pointer border-2 border-white shadow-lg hover:bg-indigo-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                    </label>
                                    <input type="file" name="avatar" id="avatar" class="hidden" onchange="previewImage(this)" accept="image/*">
                                </div>

                                <h3 class="font-bold text-lg text-gray-900">{{ $user->name }}</h3>
                                <p class="text-xs text-gray-500 mb-6">{{ $user->email }}</p>
                                
                                <button type="submit" class="w-full py-2.5 bg-gray-800 border border-transparent rounded-lg font-bold text-[11px] text-white uppercase tracking-widest hover:bg-gray-700 transition shadow-md">
                                    SIMPAN FOTO
                                </button>
                            </form>
                        </div>
                        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold text-gray-400 hover:text-red-600 transition uppercase tracking-widest">Log Out</button>
                            </form>
                        </div>
                    </div>

                    <div x-show="openSettings" x-cloak x-transition class="bg-white shadow-sm sm:rounded-xl border-l-4 border-red-500 p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <div class="md:col-span-8 flex flex-col space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-8 flex-grow">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                            
                            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 italic">⚙️ Pengaturan Lanjutan</span>
                                <button @click="openSettings = !openSettings" type="button" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-lg font-bold text-[10px] text-indigo-600 uppercase tracking-widest hover:bg-indigo-100 transition">
                                    <span x-show="!openSettings">Tampilkan</span>
                                    <span x-show="openSettings" x-cloak>Tutup</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="openSettings" x-cloak x-transition class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-8">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('preview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>