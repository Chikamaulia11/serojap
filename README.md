# Serojap

**Sistem Pelaporan Jalan Rusak Kabupaten Purwakarta**

Serojap adalah aplikasi web untuk warga Kabupaten Purwakarta melapor kerusakan jalan
(lubang, retak, genangan, jalan berlantai, dan sejenisnya) secara online, lalu
memantau progres penanganannya sampai selesai diperbaiki.

Sistem punya tiga tingkat akses: **pelapor** (warga), **admin** (petugas Punjabaran),
dan **super admin** (pengelola akun). Landing page di `/` terbuka untuk umum —
statistik dan daftar laporan terbaru bisa dilihat tanpa login, dan nama pelapor
tidak pernah ditampilkan di halaman publik.

---

## Fitur Utama

| Fitur | Keterangan |
| --- | --- |
| Landing page publik | Statistik real (total laporan, sedang diproses, selesai, rata-rata lama penanganan), 4 langkah cara kerja, contoh laporan terbaru, dan FAQ — tanpa perlu login |
| Submit laporan | Pelapor mengirim foto, titik lokasi (latitude/longitude) via peta Leaflet, alamat, dan keterangan |
| Riwayat & tracking status | Pelapor melihat status terakhir beserta catatan dan foto perbaikan dari petugas |
| Dashboard admin | Daftar laporan dengan pencarian & filter status, detail laporan, dan riwayat perubahan status |
| Update status bertingkat | `diterima` → `diproses` → `selesai`, atau `ditolak`, lengkap dengan foto perbaikan |
| Statistik admin | Jumlah laporan per status dan grafik laporan per bulan |
| Manajemen FAQ | Admin menambah/mengubah pertanyaan & jawaban yang tampil di halaman bantuan dan beranda |
| Manajemen akun | Super admin membuat, mengubah, dan menghapus akun admin serta pelapor |
| Profil & keamanan akun | Ubah profil, ubah kata sandi, hapus akun (beserta seluruh laporan & fotonya) |
| Rate limit & validasi | Submit laporan dibatasi 5 permintaan/menit dan divalidasi ketat (tipe & ukuran foto, rentang koordinat, panjang keterangan) |

---

## Role & Alur Akses

| Role | Halaman Login | Halaman Awal | Ringkasan Hak Akses |
| --- | --- | --- | --- |
| `pelapor` | `/login` | `/dashboard` | Kirim laporan, lihat riwayat laporan sendiri, buka pusat bantuan, ubah profil |
| `admin` | `/login/admin` | `/admin/dashboard` | Kelola daftar & status laporan, lihat statistik, kelola FAQ, ubah profil |
| `super_admin` | `/login/superadmin` | `/superadmin/dashboard` | Kelola seluruh akun admin & pelapor |

Role disimpan pada kolom `role` di tabel `users` (enum: `pelapor`, `admin`,
`super_admin`) dan diperiksa lewat `User::hasRole()`.

Aturan akses:

- Masing-masing role punya halaman login sendiri. Akun dengan role yang salah akan
  otomatis logout dan ditolak dengan pesan error.
- Area admin dilindungi middleware `admin`. Jika super admin membuka area admin,
  ia diarahkan ke dashboard super admin.
- Rute terbuka untuk umum: `/` (beranda), `/login*`, `/register`, dan `/up` (health check).

---

## Tech Stack

- **Backend:** PHP `^8.2`, Laravel `^12.0`
- **Frontend:** Blade + Bootstrap (CSS di `public/assets/pelapor/css/index.css`), Alpine.js, Tailwind CSS (khusus halaman admin), Leaflet (peta lokasi), SweetAlert2 (notifikasi)
- **Asset bundler:** Vite 7
- **Database:** MySQL / MariaDB (produksi), SQLite (testing & CI)
- **Pengujian:** PHPUnit 11

---

## Instalasi Lokal

### Kebutuhan

- PHP 8.2 atau lebih baru, dengan ekstensi `pdo_mysql` (atau `pdo_sqlite` bila
  memakai SQLite), `mbstring`, `openssl`, `tokenizer`, `xml`, `fileinfo`
- Composer 2
- Node.js 20+ & npm
- MySQL 5.7+ / MariaDB 10.3+, atau SQLite

### Langkah

```bash
# 1. Clone repository
git clone <url-repo> serojap
cd serojap

# 2. Install dependency PHP
composer install

# 3. Install dependency JavaScript
npm install

# 4. Siapkan file environment
cp .env.example .env

# 5. Isi konfigurasi database di .env sesuai mesin lokal
#    Opsi A, SQLite (paling cepat, tanpa service tambahan):
#    DB_CONNECTION=sqlite
#    touch database/database.sqlite
#
#    Opsi B, MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=serojap
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Buat application key
php artisan key:generate

# 7. Hubungkan folder upload agar foto laporan bisa diakses publik
php artisan storage:link

# 8. Jalankan migration (dan seed bila perlu data contoh)
php artisan migrate --seed

# 9. Build asset untuk production, atau jalankan Vite saat develops
npm run build      # production
npm run dev        # development, hot reload

# 10. Jalankan aplikasi
php artisan serve
```

Buka `http://localhost:8000`.

Alternatif menjalankan semuanya sekaligus (server + queue + log + Vite):

```bash
composer run dev
```

### Akun Demo

`php artisan migrate --seed` mengisi data contoh untuk pengembangan lokal:

| Peran          | Email                      | Kata sandi |
| -------------- | -------------------------- | ---------- |
| Super admin    | `superadmin@serojap.test`  | `password` |
| Admin          | `admin@serojap.test`        | `password` |
| Admin lapangan | `petugas@serojap.test`     | `password` |
| Pelapor        | `pelapor@serojap.test`     | `password` |

Masing-masing halaman login punya alamat sendiri: `/login` (pelapor),
`/login/admin`, dan `/login/superadmin`.

Seeder demo hanya untuk lokal. Jangan menjalankan `migrate:fresh --seed` di
server produksi karena akan menghapus seluruh data.

### Membuat Akun

- **Pelapor** bisa mendaftar sendiri lewat `/register`.
- **Admin** dan **super admin** dibuat oleh super admin lewat menu
  Manajemen Akun (`/superadmin/accounts`).

---

## Menjalankan Test

```bash
php artisan test
```

Test berjalan di atas SQLite in-memory, jadi tidak perlu MySQL aktif. Untuk
menguji satu file atau satu method:

```bash
php artisan test --filter=ReportSubmissionTest
php artisan test tests/Feature/Admin/StatistikTest.php
```

Cakupan test yang ada:

| File Test | Cakupan |
| --- | --- |
| `tests/Feature/ReportSubmissionTest.php` | Submit laporan valid, status awal otomatis, validasi foto/koordinat/keterangan, rate limit, hak akses |
| `tests/Feature/Admin/LaporanManagementTest.php` | Daftar & detail laporan admin, update status + foto perbaikan, penolakan akses role lain |
| `tests/Feature/Admin/StatistikTest.php` | Angka statistik dan grafik per bulan sesuai data yang di-seed |
| `tests/Feature/PublicLandingPageTest.php` | Beranda terbuka untuk guest, statistik real, dan tidak bocornya data pelapor |
| `tests/Feature/Auth/*` | Login per role, registrasi, ganti kata sandi, verifikasi email |
| `tests/Feature/ProfileTest.php` | Ubah profil dan hapus akun beserta seluruh laporannya |
| `tests/Feature/RuntimeSmokeTest.php` | Semua halaman publik, pelapor, admin, dan super admin benar-benar bisa dirender, termasuk verifikasi `@vite` |

---

## Struktur Folder Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PublicController.php        Beranda publik: statistik, laporan terbaru, FAQ
│   │   ├── DashboardController.php     Dashboard & ringkasan milik pelapor
│   │   ├── ReportController.php        Form & proses submit laporan pelapor
│   │   ├── ProfileController.php       Ubah profil & hapus akun
│   │   ├── Auth/                       Login (pelapor/admin/super admin), register,
│   │   │                               verifikasi email, konfirmasi & ubah kata sandi
│   │   ├── Admin/                      Area admin (middleware `admin`)
│   │   │   ├── AdminDashboardController.php  Ringkasan dashboard admin
│   │   │   ├── LaporanController.php        Daftar, detail, update status, riwayat status
│   │   │   ├── StatistikController.php      Statistik laporan per status & per bulan
│   │   │   ├── TabelFaqController.php       CRUD FAQ
│   │   │   ├── AdminProfileController.php   Profil admin
│   │   │   └── AdminAccountController.php   (belum dipakai, lihat catatan di bawah)
│   │   ├── SuperAdmin/
│   │   │   └── AccountController.php    Manajemen akun admin & pelapor
│   │   └── Pelapor/
│   │       └── FaqController.php       Pusat bantuan / FAQ untuk pelapor
│   ├── Middleware/
│   │   ├── PelaporMiddleware.php       Batasi area pelapor
│   │   ├── AdminMiddleware.php         Batasi area admin
│   │   ├── SuperAdminMiddleware.php    Batasi area super admin
│   │   └── RolePelapor.php             (belum dipakai, lihat catatan di bawah)
│   └── Requests/                       Form request (validasi terpusat)
├── Models/
│   ├── User.php                        User + role (hasRole)
│   ├── Report.php                      Laporan jalan (relasi user, statuses, latestStatus)
│   ├── TabelStatus.php                 Riwayat status tiap laporan
│   └── TabelFaq.php                    FAQ
config/                 Konfigurasi Laravel
database/
├── migrations/         Struktur tabel
├── factories/          User, Report, TabelStatus
└── seeders/            Data awal
lang/id/                Pesan validasi & atribut Bahasa Indonesia
resources/
├── views/              Blade: welcome, pelapor, admin, superadmin, auth, profile
├── css/app.css         Entrypoint Vite
└── js/app.js           Entrypoint Vite
routes/
├── web.php             Seluruh route aplikasi
└── auth.php            Route verifikasi email & kata sandi
tests/
├── Feature/            Feature test (termasuk area Admin/)
├── Unit/               Unit test
└── Concerns/           Trait pendukung (pembuat file gambar palsu)
public/assets/          Asset statis (Bootstrap, ikon, font) — bukan hasil build Vite
```

### Model Utama

| Model | Tabel | Keterangan |
| --- | --- | --- |
| `User` | `users` | `role`: `pelapor` / `admin` / `super_admin` |
| `Report` | `reports` | `user_id`, `nama_pelapor`, `foto`, `alamat`, `latitude`, `longitude`, `keterangan` |
| `TabelStatus` | `tabel_status` | `report_id`, `user_id`, `status`, `keterangan`, `foto_perbaikan`. Nilai `status`: `diterima`, `diproses`, `selesai`, `ditolak` |
| `TabelFaq` | `tabel_faq` | `user_id`, `pertanyaan`, `jawaban`, `urutan` |

Status terakhir sebuah laporan diambil lewat relasi `Report::latestStatus()`
(alias `statusTerbaru()`), yaitu baris `tabel_status` dengan `MAX(id_status)`
untuk laporan tersebut.

---

## Continuous Integration

`.github/workflows/tests.yml` menjalankan `composer install` + `php artisan test`
di atas SQLite in-memory pada setiap `push` dan `pull_request` ke branch utama.
Workflow akan merah bila ada test yang gagal.

---

## Catatan Desain yang Disengaja

Behavior di bawah ini **sengaja** begitu dan terlihat seperti bug. Jangan
"perbaiki" tanpa membaca dulu bagian ini.

### 1. Rate limit submit laporan itu dua lapis, bukan `throttle` biasa

`POST /report` punya dua pembatas dengan tugas berbeda:

| Lapis | Letak | Batas | Yang dihitung |
| --- | --- | --- | --- |
| Pagar luar | Middleware `throttle:20,1` di route | 20 request / menit | **Semua** request, termasuk yang gagal validasi |
| Anti-spam | `RateLimiter` manual di `ReportController@store` | 5 laporan / menit per user+IP | **Hanya laporan yang benar-benar tersimpan** |

Kalau `throttle:5,1` dipasang langsung di route, setiap percobaan gagal
validasi ikut menghabiskan kuota. Coba-coba orang yang sedang memperbaiki satu
field saja bisa langsung kena HTTP 429 tanpa tahu kenapa. Karena itu lapis
manual dipanggil **setelah** `$request->validate()` selesai, dan
`RateLimiter::hit()` hanya jalan kalau insert-nya benar-benar berhasil.

Tiga test di `ReportSubmissionTest` mengunci perilaku ini — lihat
`test_percobaan_gagal_validasi_tidak_menghitung_limiter`,
`test_laporan_valid_ke_enam_kenai_limiter_manual_bukan_middleware`, dan
`test_throttle_route_tetap_jalan_sebagai_pagar_luar`.

### 2. Login dengan role salah diarahkan ke dashboard sendiri, bukan logout

Middleware role (`AdminMiddleware`, `PelaporMiddleware`,
`SuperAdminMiddleware`) tidak melakukan logout saat role-nya tidak cocok.
Pengguna diarahkan ke dashboard sesuai role-nya sendiri, lengkap dengan
pesan penjelas di `session('error')`. Alasannya, kejadian itu hampir selalu
karena orang **salah klik** — membuka `/admin/laporan` padahal ia pelapor —
bukan karena sesi yang diretas. Mem-logout pengguna karena salah klik akan
membuat mereka harus login ulang dan menutupi fakta bahwa halaman itu memang
bukan untuknya.

Bedakan dengan dua kasus yang **memang** melakukan logout: belum login, dan
akunnya dinonaktifkan (`bisaLogin()` false).

### 3. `User` memakai `SoftDeletes`, bukan hard delete

Akun yang dihapus dari halaman profil hanya diberi `deleted_at`. Barisnya
tetap ada karena `SuperAdmin\AccountController@restore()` memakainya untuk
menghidupkan kembali akun (`User::withTrashed()` lalu `restore()`), dan
`unique(['email', 'deleted_at'])` ada supaya tidak bentrok dengan akun aktif.

Yang di-*hard* delete tetap data yang tidak perlu diaudit: laporan, riwayat
status di `tabel_status`, dan file fotonya. Jadi test profil memakai
`assertSoftDeleted`, bukan `assertNull($user->fresh())`.

### 4. Submit laporan sukses diarahkan ke "Riwayat Laporan", bukan balik ke form

Setelah laporan tersimpan, pengguna diarahkan ke `laporan.my-report`, bukan
kembali ke form kosong. Alasannya, begitu submit berhasil pengguna justru
membutuhkan **nomor referensi** untuk memantau progres — dan nomor itu
ditampilkan di halaman riwayat, lengkap dengan status dan fotonya.

Kembali ke form akan terasa seperti submit-nya gagal
padahal sudah berhasil, dan memaksa pengguna membuka menu lain untuk
mencari nomornya.

---

## Catatan Teknis

Beberapa hal yang perlu diketahui saat mengembangkan project ini:

- **Privasi pelapor.** Halaman `/` mengambil data laporan dengan `select()` kolom
  tertentu saja (`id`, `alamat`, `keterangan`, `created_at`), sehingga
  `nama_pelapor`, `foto`, dan `user_id` tidak pernah masuk ke view publik. Ada
  test yang menjaga hal ini (`PublicLandingPageTest`).
- **Dukungan multi-driver.** Test & CI memakai SQLite, sedangkan produksi memakai
  MySQL. Karena itu query yang spesifik MySQL (misalnya penyesuaian `ENUM`) sudah
  dibuat driver-aware. Bila menambah query baru, hindari fungsi yang hanya ada
  di MySQL tanpa perlu menangani driver lain secara manual.
- **Penghapusan akun.** `ProfileController@destroy` menghapus laporan, riwayat
  status, dan file fotonya secara permanen, lalu menandai akunnya dengan
  `deleted_at` (lihat [poin 3](#3-user-memakai-softdeletes-bukan-hard-delete)).
  Saat ini tidak ada konfirmasi kata sandi pada alur ini.

---

## Lisensi

Proyek ini milik Kabupaten Purwakarta dan digunakan untuk keperluan pelayanan publik.
