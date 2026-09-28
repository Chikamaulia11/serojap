{{--
    Komponen picker tema.

    Satu salinan markup untuk semua layout, tapi boleh dirender LEBIH
    DARI SATU kali dalam satu halaman: navbar desktop dan menu mobile
    butuhDua elemen terpisah, bukan satu elemen yang dipindah lewat
    CSS. Karena itu semua id diturunkan dari `$variant` supaya tidak
    bentrok, dan penandaan untuk JavaScript memakai kelas
    (`.theme-dd`, `.theme-dd-btn`, `.theme-dd-panel`, `.swatch`)
    yang boleh diulang sebanyak pun -- `theme.js` memindai semuanya.

    Penempatan (lihat `resources/css/theme-picker.css`):
      - `nav`     di dalam navbar, tampil >= 640px
      - `menu`    di dalam menu mobile, tampil < 640px
      - `sidebar` di sidebar admin/superadmin, dekat profil/Keluar
      - `guest`   di halaman auth, di atas kartu
      - `hero`    di landing page, di baris tombol

    Posisinya TIDAK lagi `fixed` di pojok kanan bawah. Panel tetap
    `position: absolute` di dalam `.theme-dd` (yang `relative`), jadi
    ia mengikuti tempat pemicunya dan tidak pernah terpotong oleh
    `overflow` di luarnya. `theme.js` masih mengoreksi posisi kalau
    tidak ada ruang di bawah atau di samping.
--}}
@php
    $variant = $variant ?? 'default';
    $uid = 'theme-dd-' . $variant;
@endphp

<div class="theme-dd theme-dd--{{ $variant }}">
    <button type="button" class="theme-dd-btn" id="{{ $uid }}-btn" aria-haspopup="true" aria-expanded="false"
        aria-controls="{{ $uid }}-panel">
        <span class="dot"></span>
        <span>Tema</span>
        <span class="chevron" aria-hidden="true">&#9662;</span>
    </button>

    <div class="theme-dd-panel" id="{{ $uid }}-panel" data-open="false" role="menu" aria-label="Pengaturan tema">
        <div class="theme-dd-label">Warna Aksen</div>

        <div class="swatches">
            <button type="button" class="swatch sw-teal" data-accent="teal" aria-label="Tema warna teal" aria-pressed="false"></button>
            <button type="button" class="swatch sw-blue" data-accent="blue" aria-label="Tema warna biru" aria-pressed="false"></button>
            <button type="button" class="swatch sw-green" data-accent="green" aria-label="Tema warna hijau" aria-pressed="false"></button>
            <button type="button" class="swatch sw-purple" data-accent="purple" aria-label="Tema warna ungu" aria-pressed="false"></button>
            <button type="button" class="swatch sw-amber" data-accent="amber" aria-label="Tema warna amber" aria-pressed="false"></button>
            <button type="button" class="swatch sw-rose" data-accent="rose" aria-label="Tema warna rose" aria-pressed="false"></button>
        </div>

        <div class="theme-dd-label">Mode</div>

        <div class="mode-seg">
            <button type="button" data-mode="light" aria-pressed="false">Light</button>
            <button type="button" data-mode="dark" aria-pressed="false">Dark</button>
            <button type="button" data-mode="system" aria-pressed="false">Sistem</button>
        </div>
    </div>
</div>
