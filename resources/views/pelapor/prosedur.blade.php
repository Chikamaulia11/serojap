@extends('layouts.app')

@section('title', 'Prosedur Pelaporan')
@section('deskripsi', 'Alur lengkap pelaporan kerusakan jalan di Purwakarta, dari laporan dikirim sampai perbaikan selesai.')

@section('content')

<div style="max-width: 900px; margin: 0 auto; padding: 10px 20px 40px 20px;">

    <div style="text-align: center; margin-bottom: 35px;">
        <span style="display: inline-block; padding: 6px 16px; border-radius: 999px; background: #e0f2fe; color: #075985; font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
            Prosedur
        </span>
        <h2 style="margin: 14px 0 0 0; font-size: 28px; font-weight: 700; color: #0f172a;">
            Alur Sistem Pelaporan
        </h2>
        <p style="margin: 12px auto 0 auto; max-width: 640px; font-size: 16px; line-height: 1.7; color: #475569; text-align: left;">
            Berikut alur lengkap pelaporan kerusakan jalan di Purwakarta, mulai dari laporan dikirim
            hingga penanganan selesai. Alur ini memastikan setiap laporan dipantau dan
            ditindaklanjuti dengan cepat serta transparan.
        </p>
    </div>

    <div style="margin-bottom: 30px;">
        <img
            src="{{ asset('assets/pelapor/images/alur.jpeg') }}"
            alt="Alur Sistem Pelaporan"
            style="display: block; width: 100%; max-width: 520px; margin: 0 auto; height: auto; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"
        >
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">

        @php
            $langkah = [
                [
                    'judul' => '1. Laporan dikirim',
                    'teks' => 'Pengguna mengirimkan laporan lengkap dengan lokasi, jenis kerusakan, dan foto pendukung melalui sistem.',
                ],
                [
                    'judul' => '2. Laporan diterima',
                    'teks' => 'Laporan masuk, tercatat dalam sistem, dan pelapor dapat langsung memantau status awal aduan tersebut melalui menu Riwayat.',
                ],
                [
                    'judul' => '3. Laporan diproses',
                    'teks' => 'Tim melakukan verifikasi, pengecekan lokasi, dan identifikasi pengaduan.',
                ],
                [
                    'judul' => '4. Perbaikan selesai',
                    'teks' => 'Perbaikan selesai dilaksanakan, pengguna bisa melihat foto hasilnya langsung di menu Riwayat.',
                ],
            ];
        @endphp

        @foreach ($langkah as $item)
            <div style="padding: 20px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #075985;">
                <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: 600; color: #0f172a;">
                    {{ $item['judul'] }}
                </h4>
                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">
                    {{ $item['teks'] }}
                </p>
            </div>
        @endforeach

    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a
            href="{{ route('laporan.create') }}"
            style="display: inline-block; padding: 13px 30px; background: #075985; color: #ffffff; border-radius: 10px; font-size: 15px; font-weight: 600; text-decoration: none;"
        >
            Buat Laporan Sekarang
        </a>
    </div>

</div>

@endsection
