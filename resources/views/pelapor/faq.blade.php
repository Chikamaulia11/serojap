@extends('layouts.app')

@section('title', 'Pusat Bantuan')
@section('deskripsi', 'Kumpulan pertanyaan yang sering diajukan warga tentang pelaporan kerusakan jalan di Purwakarta.')

@section('content')

<div style="max-width: 900px; margin: 0 auto; padding: 10px 20px 40px 20px;">

    <div style="text-align: center; margin-bottom: 35px;">
        <span style="display: inline-block; padding: 6px 16px; border-radius: 999px; background: var(--accent-tint); color: var(--accent-ink); font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;">
            Pusat Bantuan
        </span>
        <h2 style="margin: 14px 0 0 0; font-size: 28px; font-weight: 700; color: var(--ink);">
            FAQ
        </h2>
        <p style="margin: 12px auto 0 auto; max-width: 640px; font-size: 16px; line-height: 1.7; color: var(--ink-soft); text-align: left;">
            Kumpulan pertanyaan yang sering diajukan warga tentang pelaporan kerusakan jalan
            di Purwakarta.
        </p>
    </div>

    @if($faqs->isNotEmpty())
        {{-- Halaman ini menggantikan dashboard sebagai daftar FAQ lengkap, jadi
             harusnya bisa dicari. Tanpa ini, pengguna harus menggulir
             semua entri secara manual. Pencarian tetap client-side:
             jumlah FAQ kecil dan ini tidak butuh round-trip. --}}
        <div style="margin: 0 auto 24px; max-width: 640px;">
            <label for="cari-faq" style="display: block; font-size: 14px; font-weight: 600; color: var(--ink); margin-bottom: 6px;">
                Cari pertanyaan
            </label>
            <input
                type="search"
                id="cari-faq"
                placeholder="Contoh: foto, lokasi, status, akun"
                autocomplete="off"
                class="field-input"
            >
            <p id="hasil-cari" role="status" aria-live="polite"
               style="font-size: 13px; color: var(--ink-soft); margin: 8px 0 0;"></p>
        </div>
    @endif

    <div id="daftar-faq">
    @forelse ($faqs as $faq)
        <details
            class="faq-entri"
            style="margin-bottom: 12px; background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 16px 18px;"
        >
            <summary style="cursor: pointer; font-size: 16px; font-weight: 600; color: var(--ink);">
                {{ $faq->pertanyaan }}
            </summary>
            <p style="margin: 12px 0 0 0; font-size: 14px; line-height: 1.7; color: var(--ink-soft); white-space: pre-line;">
                {{ $faq->jawaban }}
            </p>
        </details>
    @empty
        <div style="padding: 30px 20px; text-align: center; background: var(--bg); border-radius: 10px; border: 1px dashed var(--line);">
            <p style="margin: 0 0 6px 0; font-size: 16px; font-weight: 600; color: var(--ink);">
                Belum ada pertanyaan yang tersedia
            </p>
            <p style="margin: 0; font-size: 14px; color: var(--ink-soft);">
                Admin belum menambahkan FAQ. Silakan kembali lagi nanti.
            </p>
        </div>
    @endforelse
    </div>

    <p id="faq-kosong" hidden
       style="padding: 30px 20px; text-align: center; background: var(--bg); border-radius: 10px; border: 1px dashed var(--line);">
        <span style="display: block; margin-bottom: 6px; font-size: 16px; font-weight: 600; color: var(--ink);">
            Tidak ada pertanyaan yang cocok
        </span>
        <span style="font-size: 14px; color: var(--ink-soft);">
            Coba kata kunci lain, atau <a href="{{ route('pelapor.faq') }}" style="color: var(--accent-ink); font-weight: 600;">hapus pencarian</a>.
        </span>
    </p>

</div>

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('cari-faq');

        if (!input) return;

        var entri = Array.from(document.querySelectorAll('.faq-entri'));
        var info = document.getElementById('hasil-cari');
        var kosong = document.getElementById('faq-kosong');

        input.addEventListener('input', function () {
            var keyword = this.value.trim().toLowerCase();
            var cocok = 0;

            entri.forEach(function (item) {
                var ada = keyword === '' || item.textContent.toLowerCase().indexOf(keyword) !== -1;

                item.hidden = !ada;

                if (ada) cocok++;
            });

            kosong.hidden = cocok > 0;

            info.textContent = keyword === ''
                ? ''
                : cocok + ' dari ' + entri.length + ' pertanyaan cocok dengan "' + keyword + '".';
        });
    })();
</script>
@endpush

@endsection
