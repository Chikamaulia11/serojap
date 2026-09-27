@props([
    'disabled' => false,
    'loadingText' => 'Menyimpan...',
    'type' => 'submit',
    'validate' => null,
])

{{--
    Tombol submit yang menonaktifkan dirinya sendiri begitu diklik.

    Tanpa ini, satu klik lambat bisa terkirim dua kali. Untuk kirim
    laporan ini berarti dua foto 5 MB terunggah dan dua baris riwayat
    dibuat; untuk ubah status berarti dua entri audit dengan isi sama.

    `validate` opsional: nama fungsi global yang menerima event dan
    mengembalikan false kalau validasi gagal. Fungsinya dipanggil
    SEBELUM `busy` diset, jadi validasi yang gagal tidak pernah
    mengunci tombol dalam keadaan "Menyimpan..." selamanya.

    Jangan pakai `onclick="return validateForm(event)"` di sini. Inline
    handler berjalan berdampingan dengan `x-on:click`, jadi `busy`
    bisa sudah true saat validasi menolak -- persis bug yang paling
   merepotkan karena gejalanya tombol mati total.
--}}
<button
    {{-- `type="{{ $type }}"`, bukan dirangkai string di dalam `{{ }}`.

         `{{ }}` meng-escape keluarannya, jadi menulis
         `'type="' . $type . '"'` menghasilkan `type="&quot;submit&quot;"`.
         Nilai itu bukan tipe tombol yang sah, jadi browser diam-diam
         memakai default `submit` -- termasuk saat pemanggil mengirim
         `type="button"`. Tombol "button" pun diam-diam jadi submit.
         `matches('button[type=submit]')` juga ikut gagal, sehingga test
         dan selector CSS tidak pernah menemukan tombolnya.
    --}}
    type="{{ $type }}"
    @disabled($disabled)
    @if($type === 'submit')
        x-data="{ busy: false }"
        {{--
            Guard-nya menolak dilekatkan ke `submit` di elemen TOMBOL:
            event `submit` memang dilepas pada <form> dan tidak merambat
            ke button di dalamnya, jadi tidak pernah terpanggil sama
            sekali. `busy` tetap false, tidak ada spinner, dan nol
            double-submit yang prevented.

            Karena itu guard-nya di `click`, yang pasti terpanggil.
            `reportValidity()` dicek lebih dulu supaya klik pada form
            yang gagal validasi HTML5 tidak mengunci tombol selamanya.

            `this` TIDAK dipakai di sini. Di Alpine, `this` di dalam
            `x-on`/`x-init` menunjuk ke scope data, bukan ke elemen,
            jadi `this.form` selalu `undefined` dan seluruh pemeriksaan
            di bawahnya mati diam-diam. Untuk sampai ke elemen, magic
            `$el` yang dipakai.

            Setelah itu `busy` = true dan tombol benar-benar disabled
            selama request berjalan -- inilah yang benar-benar menahan
            klik kedua. `preventDefault()` saja tidak cukup, karena
            form yang sudah terkirim sekali sudah meninggalkan halaman.
        --}}
          x-on:click="if (busy) { $event.preventDefault(); return }
                     if ($el.form && !$el.form.reportValidity()) { return }
                     @if($validate)if (!{{ $validate }}($event)) { return }@endif
                     busy = true"
          {{--
              Validasi kustom (`public/js/form.js`) berjalan DI DALAM
              event `submit` dan menolak dengan `preventDefault()`. Jadi
              ada jeda antara klik dan keputusan validasi: `busy` sudah
              `true`, tombol sudah nonaktif, lalu submit-nya dibatalkan
              -- dan `busy` tidak pernah dikembalikan. Tombol terkunci
              permanen di "Mengunggah..." padahal laporan tidak pernah
              terkirim. Satu-satunya jalan keluar: muat ulang halaman.

              Pemeriksaan ditunda ke microtask dengan sengaja. Kalau
              `defaultPrevented` dibaca langsung di dalam listener, hasilnya
              bergantung pada urutan pendaftaran: listener yang kebetulan
              lebih dulu masih melihat `false` karena `preventDefault()`
              dari listener lain belum dipanggil. Setelah semua listener
              selesai, nilainya sudah final dan tidak ada yang bisa
              berubah dalam turn yang sama.
          --}}
          x-init="if ($el.form) { $el.form.addEventListener('submit', function (e) { Promise.resolve().then(function () { if (e.defaultPrevented) { busy = false; } }); }); }"

        x-bind:disabled="busy"
        x-bind:aria-busy="busy ? 'true' : null"
    @endif
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed']) }}
>
    @if($type === 'submit')
        <svg
            x-show="busy"
            x-cloak
            class="w-4 h-4 animate-spin"
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    @endif

    <span x-show="!busy" x-cloak>{{ $slot }}</span>
    <span x-show="busy" x-cloak>{{ $loadingText }}</span>
</button>
