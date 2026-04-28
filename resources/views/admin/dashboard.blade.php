<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Khusus Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat Datang, Boss!") }} <br>
                    Anda masuk sebagai: <span class="font-bold text-blue-600">{{ auth()->user()->role }}</span>
                </div>
            </div>
            
            <div class="mt-4 bg-yellow-100 border-l-4 border-yellow-500 p-4">
                <p class="text-sm text-yellow-700">
                    Info: Ini adalah halaman sementara. Kamu bisa ganti isinya dengan desain dashboard admin yang sudah dibuat teman timmu nanti.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>