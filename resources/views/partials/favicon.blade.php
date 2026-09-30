{{--
    Tag favicon, dipanggil dari setiap halaman yang punya `<head>` sendiri.

    Kenapa partial dan bukan ditulis di tiap layout: `favicon.ico` dulunya
    0 byte dan TIDAK ADA tag `<link rel="icon">` di mana pun di situs
    ini. Browser tetap meminta `/favicon.ico` dan mendapat berkas kosong,
    jadi tidak ada ikon sama sekali -- tapi tidak ada juga satu pun
    tempat di source yang bisa disalahkan, karena tidak ada kode yang
    salah. Sekarang tag-nya ada di enam halaman sekaligus, dan kalau
    logo suatu saat berubah, cukup ganti di sini.

    Isi `favicon.ico` (16/32/48) dibuat dari lotus yang sama dengan
    komponen logo: petal dipotong rapat ke bounding box isinya, karena
    kanvas webp 192x191 punya padding transparan yang besar dan kalau
    dipakai mentah petalnya hanya memenuhi sebagian kecil tab.
--}}
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
