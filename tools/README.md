# Pemeriksaan Statis

Lima skrip di `tools/` menggantikan sebagian pekerjaan yang biasanya
dikerjakan `php -l` dan `node --check`. Semuanya berbasis Python 3
tanpa dependensi.

| Perintah | Yang diperiksa |
|---|---|
| `python3 tools/check_php.py` | Keseimbangan kurung, heredoc, impor mati |
| `python3 tools/check_blade_tags.py` | Keseimbangan tag HTML di view Blade |
| `python3 tools/check_blade_compile.py` | View Blade benar-benar bisa dikompilasi |
| `python3 tools/check_js.py` | Keseimbangan kurung JS, string, `console.log` |
| `python3 tools/audit_refs.py` | Rujukan lintas file: view, route, komponen, layout |

`check_blade_compile.py` satu-satunya yang butuh PHP (dia memakai Blade
compiler milik aplikasi). Set `PHP_BIN` kalau `php`/`php.exe` tidak ada
di `PATH`.

Kirimkan path tertentu untuk mempersempit:

```bash
python3 tools/check_php.py app/Models/Report.php
python3 tools/check_blade_tags.py resources/views/admin/faq/
```

Semuanya mengembalikan exit code `1` kalau ada temuan, jadi bisa
dirangkai di pre-commit.

## Kenapa perlu

Lingkungan ini tidak punya PHP maupun Node, padahal dua hal itu
justru yang pertama kali turun saat Blade atau JavaScript rusak.

Kasus yang sudah ketahuan dan hanya terlihat lewat pemeriksaan
seperti ini:

- `admin/laporan/update-status.blade.php` memakai `$laporan` sebagai
  Paginator, padahal controller mengirimnya sebagai satu model
  `Report` -- memanggil `->total()` di sana error fatal.
- `admin/faq/index.blade.php` memakai `$faqs->count()` pada
  `LengthAwarePaginator`, jadi badge selalu tertahan di 20 begitu
  data bertambah. Plus tidak ada paginasi sama sekali, jadi FAQ
  setelah 20 item pertama tidak pernah bisa dibuka.
- `x-flash-message` menulis dua atribut `class` dalam satu tag, dan
  browser membuang yang kedua beserta semua class dari pemanggil.
- `x-submit-button` memasang `x-on:submit` pada elemen `<button>`.
  Event `submit` dipancarkan di `<form>` dan tidak mengalir ke
  button, jadi proteksi double-submit tidak pernah aktif.
  - `admin/laporan/show.blade.php` punya satu `<div>` pembuka yang
    tidak pernah ditutup, sehingga lightbox gambar ikut ter-nest di
    dalam wrapper halaman.

  Semuanya lolos pemeriksaan directive Blade yang biasa (@if/@endif
  tetap seimbang), jadi pemeriksaan directive saja tidak cukup --
  itulah alasan `check_blade_tags.py` ada.

## `audit_refs.py`

Tiga skrip di atas hanya melihat satu file. `audit_refs.py` melihat
seluruh aplikasi sekaligus dan menjawab empat pertanyaan:

1. Setiap `view('x.y')` di controller benar-benar punya file-nya.
2. Setiap `route('x.y')` di Blade terdaftar di `routes/`.
3. Setiap `<x-nama>` punya file di `resources/views/components/`.
4. Setiap `@extends`/`@include` menunjuk layout yang ada.

Untuk nomor 2, skrip ini membaca `Route::resource` di dalam group
ber-`->name('superadmin.')` dan memperhitungkan `->only()`,
`->except()`, serta `->names()`. Tanpa itu semua route
`superadmin.accounts.*` akan dilaporkan hilang padahal jelas ada.

### Yang ditemukan di sesi terakhir

Temuan yang paling parah di sini tidak involve sintaks sama sekali:

- **`x-guest-layout` tidak pernah ada.** Delapan halaman auth --
  termasuk `auth/login.blade.php` -- memanggilnya. Hasilnya
  `Unable to locate a class or view for component [guest-layout]`
  di halaman login utama, jadi aplikasi tidak bisa dipakai sama
  sekali. Balanced-tag check tetap hijau karena `<x-guest-layout>`
  memang tag yang seimbang.
- **`resources/views/dashboard.blade.php` (sisa Breeze) memakai
  `<x-app-layout>` yang juga tidak ada.** Tidak pernah keluar sebagai
  error karena `DashboardController` mengembalikan
  `pelapor.dashboard`, bukan `dashboard` -- file itu benar-benar
  mati tapi masih menggantung.
- **`AdminAccountController` tidak punya route dan view.** Sidebar
  admin masih menautkan `route('admin.admin-accounts.index')`.
    Blok itu tidak akan pernah dievaluasi karena `AdminMiddleware`
    mengalihkan super admin, tapi `route()` di dalamnya akan meledak
    begitu kondisinya berubah.
- **Sisa Breeze lain** yang tidak dirujuk siapa pun dan sudah
  dihapus: `<x-danger-button>`, `<x-modal>`, `<x-secondary-button>`,
  `layouts/navigation.blade.php`, `admin/statistik/statistik.blade.php`,
  `admin/laporan/partials/pilih-laporan-dropdown.blade.php`, dan dua
  partial di `profile/partials/`.

## Audit tema di browser: `cdp.js` + `audit_theme.py` + `probe_picker.py` + `shot_theme.py`

Empat skrip ini mengukur halaman asli di Chrome, bukan meniru. Halaman
dirender oleh `tests/Feature/ThemeAuditDumpTest.php` ke
`public/theme-audit/`, disajikan lewat HTTP lokal supaya
`/build/assets/...` dari `@vite` termuat, lalu diukur.

| Skrip | Tugas |
| --- | --- |
| `cdp.js` | Browser sungguhan di bawah DevTools Protocol |
| `audit_theme.py` | Kontras teks + overflow, 25 halaman x 12 kombinasi |
| `probe_picker.py` | Geometri panel picker per varian |
| `shot_theme.py` | Screenshot untuk diperiksa manusia |

`cdp.js` adalah satu-satunya yang bukan Python. Bukan karena
dipilih, tapi karena harus listening di sisi Windows: Chrome
menjalankan di host Windows, dan `node.exe` adalah proses Windows
sekaligus bisa bicara WebSocket ke DevTools-nya. Python di WSL tidak
bisa menjangkau loopback Windows, jadi CDP dari sisi WSL selalu
berakhir `Connection refused`.

`node.exe` juga membuat path-nya harus versi Windows (`wslpath -w`);
path POSIX berakhir sebagai `MODULE_NOT_FOUND`.

### Dua jebakan yang sudah bikin hasil audit lama salah

**1. `--window-size=375` sebenarnya 500.** Chrome di Windows tidak
bisa membuat jendela lebih sempit dari 500px, jadi semua audit
"375px" yang lama sebenarnya mengukur 500px lalu diberi label 375,
dan `--headless=old` yang biasa dipakai untuk membuat scrollbar
overlay juga sudah tidak ada di Chrome ini. Lebar sekarang
dipaksakan lewat `Emulation.setDeviceMetricsOverride`, dan
`window.innerWidth` diverifikasi, bukan diasumsikan.

Satu akibatnya: pengukuran "mobile" lama sama sekali tidak
menguji kondisi mobile. Layout mobile (`max-width: 640px`) sama
sekali tidak aktif di 500px untuk sebagian aturan.

**2. Scrollbar harus dibiarkan ada saat mengaudit overflow.** Di
emulasi CDP scrollbar vertikal klasik tetap mengambil 15px
(`clientWidth` = lebar - 15). Justru itu syaratnya: `100vw` menghitung
scrollbar juga, jadi `width: 100vw` + `margin-left: -50vw` selalu
15px lebih lebar dari area yang boleh dipakai -- bug footer yang
menghasilkan scrollbar horizontal di desktop. Kalau scrollbar
disembunyikan, bug itu lenyap dari pengukuran dan audit melaporkan
"bersih" untuk halaman yang sebenarnya bergeser.

Efeknya berlawanan pada screenshot: HP asli memakai scrollbar
overlay, jadi foto 375px dengan scrollbar klasik hanya 360px dan
tidak mewakili yang dilihat pengguna. Karena itu `shot_theme.py`
menyetel `hideScrollbars`, sementara audit tidak.

Skrip juga memeriksa elemen yang lebarnya sama dengan `100vw`
(`vwCulprits`), karena overflow `100vw` tidak selalu terlihat
langsung dari `scrollWidth > clientWidth`.

### Memakai

```bash
php artisan test --filter=ThemeAuditDumpTest   # bangun ulang dump
python3 tools/audit_theme.py                   # semua halaman
python3 tools/audit_theme.py pelapor           # hanya yang namanya cocok
python3 tools/probe_picker.py
python3 tools/shot_theme.py pelapor-dashboard teal dark --w=375
```

Dump harus dibangun ulang setiap kali Vite build: build menghapus
hash CSS lama, jadi dump lama akan mengukur kontras tanpa CSS sama
sekali.

Hasil akhir: `storage/app/theme-audit.json` dan
`storage/app/theme-shots/`.

## Batasnya

Alat-alat ini **bukan** pengganti tooling sebenarnya:

- `php -l` tetap wajib sebelum merge.
- `node --check` tetap wajib untuk JavaScript.
- `php artisan view:cache` **tidak** bisa dipakai sebagai jaminan
  Blade bisa dikompilasi: dia melaporkan sukses untuk view yang rusak.
  Yang jadi `./vendor/bin/phpunit` dan `check_blade_compile.py`.
- Keseimbangan tag bukan pemeriksaan validitas HTML. Atribut yang
  salah, `id` ganda, atau `label` tanpa `for` tetap lolos.
- Pemeriksa impor PHP bekerja di tingkat nama; dia tidak membedakan
  pemakaian di dalam komentar dari pemakaian sungguhan.
- Pemeriksaan kontras dan overflow di audit tema hanya melihat
  elemen yang punya text node. Teks di dalam `::before`/`::after`,
  gambar, dan `background-image` tidak diukur. Lapisan yang menutup
  teks (gradien, foto) juga tidak dinilai -- audit ini menghitung
  rasio warna teks versus latar, bukan warna yang benar-benar terlihat
  setelah foreground dicampur.
- Audit memuat halaman statis, jadi ia tidak menjalankan alur
  JavaScript aplikasi seperti di produksi.

Jalankan semuanya dari Windows kalau PHP dan Node tersedia:

```bash
php artisan test
php artisan view:cache
npm run build
python3 tools/check_php.py && python3 tools/check_blade_tags.py \
  && python3 tools/check_blade_compile.py && python3 tools/check_js.py
```
