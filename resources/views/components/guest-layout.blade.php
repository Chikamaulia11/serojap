{{--
    Komponen ini HILANG sementara, padahal delapan halaman auth
    memakainya:

        auth/login.blade.php          <x-guest-layout>
        auth/login-admin.blade.php    <x-guest-layout>
        auth/login-superadmin.blade.php <x-guest-layout>
        auth/register.blade.php       <x-guest-layout>
        auth/forgot-password.blade.php  <x-guest-layout>
        auth/reset-password.blade.php   <x-guest-layout>
        auth/confirm-password.blade.php <x-guest-layout>
        auth/verify-email.blade.php     <x-guest-layout>

    Semuanya di-return controller, jadi tanpa file ini SETIAP
    halaman itu -- termasuk halaman login utama -- berakhir
    "Unable to locate a class or view for component
    [guest-layout]". Websites-nya tidak bisa dipakai sama sekali.

    Isinya didelegasikan ke `layouts/guest.blade.php` yang sudah ada
    dan sudah benar (pakai `{{ $slot }}`) supaya ada satu sumber
    HTML saja. Kalau nanti layout-nya berubah, cukup ubah satu file.
--}}
@include('layouts.guest', ['slot' => $slot])
