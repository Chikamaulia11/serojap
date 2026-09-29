{{--
    Logo SEROJAP sebagai inline SVG, bukan `logo-serojap.webp`.

    Alasan mengganti raster:

    * Warna pada PNG/WebP sudah di-bake di dalam file. Logo gelap
      tetap gelap saat di light mode, dan kontrasnya terhadap
      `--surface` pun tidak pernah dihitung audit karena audit
      hanya membaca `color` dan `background-color`, bukan piksel
      gambar.
    * Sebagai SVG, warnanya mengikuti `currentColor` dan token
      CSS, jadi otomatis ikut mode dan kontrasnya bisa diukur.

    `role="img"` dengan `<title>` menjaga agar tetap terbaca
    screen reader. `aria-label` dipakai juga karena beberapa
    pembaca layar mengabaikan `<title>` pada SVG inline.

    Yang dipakai: `currentColor` (mengikuti `--ink`),
    `var(--accent)`, dan `var(--surface)` -- tidak ada warna hex,
    jadi palet tidak perlu diubah di dua tempat.
--}}
<svg {{ $attributes->merge([
        'class' => 'h-10 w-auto',
        'viewBox' => '0 0 48 48',
        'fill' => 'none',
        'xmlns' => 'http://www.w3.org/2000/svg',
        'role' => 'img',
    ]) }}
     aria-label="Logo Serojap">
    <title>Logo Serojap</title>

    {{-- Cangkang daun, warna aksen --}}
    <path d="M24 3C13.5 3 5 11.5 5 22c0 8.5 5.6 15.6 13.4 18.4l.6.2.6-.2C27.4 37.6 33 30.5 33 22 33 11.5 24.5 3 24 3Z"
          fill="var(--accent)" />

    {{-- Urat daun, warna permukaan halaman supaya tetap terbaca
         di atas aksen terang maupun gelap --}}
    <path d="M24 8v30M24 16l7-5M24 16l-7-5M24 23l8.5-5.5M24 23l-8.5-5.5"
          stroke="var(--surface)"
          stroke-width="2.4"
          stroke-linecap="round" />

    {{-- Tangkai --}}
    <path d="M24 41v4"
          stroke="var(--surface)"
          stroke-width="2.4"
          stroke-linecap="round" />
</svg>
