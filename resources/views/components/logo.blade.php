@props([
    'size' => 40,
    'decorative' => false,
    'alt' => 'Logo SEROJAP',
])

{{--
    SATU-SATUNYA sumber logo SEROJAP untuk seluruh situs.

    Kenapa komponen ini ada
    ----------------------
    Sebelumnya ada DUA logo berbeda yang sama-sama dipakai:

    1. `public/logo.png` -- lotus navy/teal di atas kanvas putih.
       Dipakai di navbar dashboard dan header form laporan.
    2. `resources/views/components/application-logo.blade.php` --
       inline SVG "benih/tunas" (cangkang warna aksen + urat daun).
       Dipakai di halaman auth, sidebar admin, sidebar superadmin,
       dan footer.

    Keduanya digambar independen, jadi bentuknya berbeda persis di
    halaman yang berbeda. Navbar pelapor jadi acuan, dan itu artwork
    yang dipakai komponen ini. Bentuknya TIDAK digambar ulang di
    sini: menyalin ulang logo berarti mengubah branding tanpa orang
    yang menyetujui. Aset aslinya dipakai apa adanya.

    Kenapa `logo-serojap.webp`, bukan `logo.png`
    -------------------------------------------
    Ketiganya artwork yang sama, sudah dicek dengan membandingkan
    mask piksel: IoU 0.86 pada penyamaan 64x64 setelah kedua berkas
    dipotong ke bounding box isinya lalu diskalakan. Bentuknya sama,
    yang berbeda hanya latar:

        logo.png           772x768, 100% opaque, putih baked-in
        logo-serojap.webp  192x191,   9.9% opaque, transparan

    Yang dipakai di sini webp karena dua alasan:

    * Kontrasnya bisa dihitung, bukan kebetulan. `logo.png` putihnya
      tertanam di dalam JPEG, jadi batas logo terhadap latar halaman
      tidak pernah bisa diukur audit. Plat di bawah yang jadi
      penanggung jawab kontras, dan `--logo-plate` bisa diukur.
    * Resolusinya cukup. 192px untuk ukuran tampil terbesar saat ini
      80px, masih 2.4x untuk layar 2x. `logo.png` 772px tidak
      menambah apa pun karena plat yang menentukan ukuran visual.

    Plat terang, dan itu disengaja
    -------------------------------
    Warna logo resmi gelap: petal luar navy (#183868), petal dalam
    teal (#107070). Di atas `--surface` mode dark (#1a2323) petal itu
    nyaris hilang. Jadi platnya dikunci terang di kedua mode lewat
    `--logo-plate` -- persis seperti warna putih yang selama ini ikut
    tertanam di `logo.png`, jadi tidak ada perubahan tampilan di
    navbar. Plat terang di mode dark bukan hal baru di situs ini:
    sidebar superadmin sudah memakai kotak putih untuk logo.

    Kalau brand suatu saat memutuskan logo perlu versi terang,
    itu aset BARU yang harus dibuat dan disetujui -- bukan filter CSS
    di komponen ini. Filter akan mengubah warna resmi dengan diam
    saja.
--}}
<span
    {{ $attributes->merge(['class' => 'serojap-logo']) }}
    style="--logo-size: {{ (int) $size }}px"
    @if($decorative) aria-hidden="true" @endif
>
    <img
        src="{{ asset('assets/pelapor/images/logo-serojap.webp') }}"
        alt="{{ $decorative ? '' : $alt }}"
        width="{{ (int) $size }}"
        height="{{ (int) $size }}"
        decoding="async"
        @if($decorative) draggable="false" @endif
    >
</span>
