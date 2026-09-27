<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pesan Atur Ulang Kata Sandi
    |--------------------------------------------------------------------------
    |
    | String ini dipakai `PasswordResetLinkController` dan
    | `PasswordResetController` lewat `trans($status)`, dengan $status
    | berupa kunci dari password broker:
    |
    |   passwords.sent      tautan berhasil dikirim
    |   passwords.user      email tidak terdaftar
    |   passwords.token     token tidak valid / kedaluwarsa
    |   passwords.throttled terlalu sering mencoba
    |   passwords.reset     kata sandi berhasil diubah
    |
    | Tanpa file ini, `APP_LOCALE=id` jatuh ke `en` dan seluruh
    | pesan tersebut muncul dalam bahasa Inggris -- tidak terlihat
    | dari kode, hanya dari halaman yang dibuka pengguna.
    |
    */

    'reset' => 'Kata sandi kamu berhasil diatur ulang.',
    'sent' => 'Tautan atur ulang kata sandi sudah dikirim ke email kamu.',
    'throttled' => 'Terlalu banyak percobaan. Tunggu beberapa saat sebelum mencoba lagi.',
    'token' => 'Tautan atur ulang kata sandi ini tidak berlaku lagi. Minta tautan baru.',
    'user' => 'Email itu tidak terdaftar di sini.',

];
