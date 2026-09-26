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
#    Contoh MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=serojap
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Buat application key
php artisan key:generate

# 7. Jalankan migration (dan seed bila perlu data contoh)
php artisan migrate --seed

# 8. Build asset untuk production, atau jalankan Vite saat develops
npm run build      # production
npm run dev        # development, hot reload

# 9. Jalankan aplikasi
php artisan serve
```

Buka `http://localhost:8000`.

Alternatif menjalankan semuanya sekaligus (server + queue + log + Vite):

```bash
composer run dev
```

### Membuat Akun

- **Pelapor** bisa mendaftar sendiri lewat `/register`.
- **Admin** dan **super admin** dibuat oleh super admin lewat menu
  Manajemen Akun (`/superadmin/accounts`).
- Seeder bawaan membuat satu akun contoh `test@example.com` dengan kata sandi
  `password` (role `pelapor`). Ganti atau hapus sebelum dipakai di produksi.

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

## Catatan Teknis

Beberapa hal yang perlu diketahui saat mengembangkan project ini:

- **Privasi pelapor.** Halaman `/` mengambil data laporan dengan `select()` kolom
  tertentu saja (`id`, `alamat`, `keterangan`, `created_at`), sehingga
  `nama_pelapor`, `foto`, dan `user_id` tidak pernah masuk ke view publik. Ada
  test yang menjaga hal ini (`PublicLandingPageTest`).
- **Rate limit.** `throttle:5,1` hanya dipasang pada `POST /report`, bukan pada
  seluruh middleware group `pelapor`, supaya navigasi halaman lain tidak terganggu.
- **Dukungan multi-driver.** Test & CI memakai SQLite, sedangkan produksi memakai
  MySQL. Karena itu query yang spesifik MySQL (misalnya penyesuaian `ENUM`) sudah
  dibuat(driver-aware). Bila menambah query baru, hindari fungsi yang hanya ada
  di MySQL tanpa perlu menangani driver lain secara manual.
- **Penghapusan akun.** `ProfileController@destroy` menghapus akun beserta seluruh
  laporan, riwayat status, dan file fotonya. Saat ini tidak ada konfirmasi
  kata sandi pada alur ini.
- **View yang belum ada.** Beberapa controller masih mereferensikan view yang belum
  dibuat, sehingga halamannya akan error sampai view-nya ditambahkan:
  - `Pelapor\FaqController@index` → `resources/views/pelapor/faq.blade.php`
  - route `/prosedur` → `resources/views/pelapor/prosedur.blade.php`
  - `Admin\AdminAccountController` → `resources/views/admin/admin-accounts/*` (controller ini belum dipakai route mana pun)

---

## Lisensi

Proyek ini milik Kabupaten Purwakarta dan digunakan untuk keperluan pelayanan publik.
