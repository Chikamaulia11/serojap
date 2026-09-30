{{--
    Script anti-FOUC.

    WAJIB disisipkan di dalam <head>, sebelum CSS pertama dimuat, di
    setiap dokumen yang punya <html> sendiri. Untuk sekarang itu
    layouts `app`, `admin`, `superadmin`, `guest`, ditambah
    `welcome.blade.php` yang berdiri sendiri.

    Kenapa harus inline dan bukan module dari Vite: module `type="module"`
    itu `defer` secara default, jadi baru jalan setelah dokumen selesai
    di-parse. CSS pun sudah ter-apply. Hasilnya tema yang salah
    terender sekali penuh lalu diganti -- kedip putih yang persis
    ingin dihindari. Script ini sinkron, jadi atribut terpasang sebelum
    browser menggambar frame pertama.

    Versinya sengaja dibuat sangat pendek dan TIDAK memakai apa pun
    dari luar, supaya tidak menambah request dan tidak bisa gagal
    dimuat.

    Nilainya harus sama persis dengan default di `resources/js/theme.js`
    (teal + system) dan daftar valid aksen/modenya juga harus sama.
    Kalau salah satu berubah, ubah yang dua-duanya.

    Kunci mode dibaca dari atribut `data-mode-locked` di tag `<html>`,
    bukan dari parameter include. Alasannya hanya satu: atribut itu
    sudah ada di HTML sebelum CSS pertama dimuat, sedangkan nilai
    include hanya hidup di PHP. Kalau keduanya berbeda, yang salah
    bisa saja yang menang, dan gejalanya berupa kedip yang sulit
    diuji.
--}}
<script>
(function () {
    var ACCENTS = ['teal', 'blue', 'green', 'purple', 'amber', 'rose'];
    var MODES = ['light', 'dark', 'system'];
    var root = document.documentElement;

    var accent = 'teal';
    var mode = 'system';

    try {
        var a = localStorage.getItem('serojap_theme_accent');
        var m = localStorage.getItem('serojap_theme_mode');
        if (ACCENTS.indexOf(a) !== -1) accent = a;
        if (MODES.indexOf(m) !== -1) mode = m;
    } catch (e) {
        /* storage tidak bisa dibaca (mode privat): pakai default */
    }

    var resolved = mode;
    if (mode === 'system') {
        resolved = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /* Kunci mode: `data-mode` di bawah tetap menyimpan preferensi asli
     * (seluruh skrip ini hanya membaca `localStorage`, tidak pernah
     * menulis), jadi pengunjung tidak kehilangan pilihan dark mode-nya. */
    var locked = root.getAttribute('data-mode-locked');
    if (locked === 'light' || locked === 'dark') {
        resolved = locked;
    }

    root.setAttribute('data-accent', accent);
    root.setAttribute('data-mode', mode);
    root.setAttribute('data-mode-resolved', resolved);
})();
</script>
