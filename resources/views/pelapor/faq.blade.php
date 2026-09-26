@extends('layouts.app')

@section('content')

<div style="max-width: 900px; margin: 0 auto; padding: 10px 20px 40px 20px;">

    <div style="text-align: center; margin-bottom: 35px;">
        <span style="display: inline-block; padding: 6px 16px; border-radius: 999px; background: #e0f2fe; color: #075985; font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
            Pusat Bantuan
        </span>
        <h2 style="margin: 14px 0 0 0; font-size: 28px; font-weight: 700; color: #0f172a;">
            FAQ
        </h2>
        <p style="margin: 12px auto 0 auto; max-width: 640px; font-size: 16px; line-height: 1.7; color: #475569; text-align: left;">
            Kumpulan pertanyaan yang sering diajukan warga tentang pelaporan kerusakan jalan
            di Purwakarta.
        </p>
    </div>

    @forelse ($faqs as $faq)
        <details
            style="margin-bottom: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px;"
        >
            <summary style="cursor: pointer; font-size: 16px; font-weight: 600; color: #0f172a;">
                {{ $faq->pertanyaan }}
            </summary>
            <p style="margin: 12px 0 0 0; font-size: 14px; line-height: 1.7; color: #475569; white-space: pre-line;">
                {{ $faq->jawaban }}
            </p>
        </details>
    @empty
        <div style="padding: 30px 20px; text-align: center; background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1;">
            <p style="margin: 0 0 6px 0; font-size: 16px; font-weight: 600; color: #0f172a;">
                Belum ada pertanyaan yang tersedia
            </p>
            <p style="margin: 0; font-size: 14px; color: #64748b;">
                Admin belum menambahkan FAQ. Silakan kembali lagi nanti.
            </p>
        </div>
    @endforelse

</div>

@endsection
