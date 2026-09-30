<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\TabelFaq;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Membuang setiap halaman aplikasi sebagai HTML statis ke
 * `public/theme-audit/`, lalu mengembalikan list file yang tertulis.
 *
 * Tujuannya memberi bahan untuk audit tema di browser sungguhan:
 * output `@vite` menunjuk ke `/build/assets/...` yang relatif, jadi
 * cukup disajikan lewat HTTP server yang root-nya `public/`, dan
 * Chrome headless bisa memuat CSS asli aplikasi -- bukan tiruan.
 *
 * Dipakai oleh `tools/audit_theme.py` (headless Chrome) untuk menghitung
 * kontras teks dan overflow horizontal. Test ini sendiri hanya menulis
 * berkas; tidak ada assertion, karena tujuannya membuat bahan audit,
 * bukan menguji perilaku.
 */
class ThemeAuditDumpTest extends TestCase
{
    use RefreshDatabase;

    private const OUT = 'public/theme-audit';

    private function isiDataUji(): void
    {
        User::factory()->create([
            'name' => 'Pelapor Demo',
            'email' => 'pelapor@serojap.test',
            'role' => 'pelapor',
        ]);
        User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@serojap.test',
            'role' => 'admin',
            'posisi' => 'Administrator Whatsapp',
        ]);
        User::factory()->create([
            'name' => 'Super Admin Demo',
            'email' => 'superadmin@serojap.test',
            'role' => 'super_admin',
            'posisi' => 'Super Admin',
        ]);

        $faq = TabelFaq::create([
            'user_id' => 1,
            'pertanyaan' => 'Bagaimana cara melapor?',
            'jawaban' => 'Lewat menu Laporan di navbar.',
            'urutan' => 1,
        ]);

        $laporan = Report::create([
            'user_id' => 1,
            'nama_pelapor' => 'Warga Trials',
            'alamat' => 'Jl. Uji Coba No. 1, Purwakarta',
            'latitude' => -6.55,
            'longitude' => 107.49,
            'keterangan' => 'Lubang jalan untuk keperluan pengujian tema.',
        ]);

        foreach (['diterima', 'diproses', 'selesai', 'ditolak'] as $status) {
            TabelStatus::create([
                'report_id' => $laporan->id,
                'user_id' => 2,
                'status' => $status,
                'keterangan' => 'Keterangan status '.$status.'.',
            ]);
        }
    }

    private function dump(string $name, string $url): void
    {
        $response = $this->get($url);
        $response->assertOk();
        $html = $response->getContent();

        // Tandai nama file di <body> supaya hasil scan bisa mengaitkan
        // setiap temuan ke halaman asalnya tanpa perlu peta terpisah.
        $html = preg_replace(
            '/<body\b/i',
            '<body data-theme-audit-page="'.$name.'"',
            $html,
            1
        );

        // `asset()` dan `@vite()` di environment test menghasilkan URL
        // absolut `http://localhost:8000/...`, sedangkan server audit
        // menyajikan `public/` di port sendiri. Semua origin localhost
        // dibikin relatif supaya `/build/assets/...` dan `/css/...`
        // benar-benar termuat saat discan.
        $html = preg_replace(
            '#\bhttps?://localhost(:\d+)?/#',
            '/',
            $html
        );

        // Guard: tanpa ini halaman audit diam-diam tampil tanpa CSS tema
        // sama sekali, dan semua hasil kontras jadi tidak bermakna.
        $this->assertStringContainsString(
            '/build/assets/',
            $html,
            'Halaman '.$name.' tidak memuat CSS hasil build Vite. '
            .'Pastikan public/build/manifest.json ada (jalankan: npm run build).'
        );

        // Guard: markup yang rusak diam-diam lolos semua pemeriksaan
        // lain, karena audit men-set atribut tema sendiri sebelum
        // mengukur. `@include` yang jatuh di tengah tag yang belum
        // tertutup -- pernah terjadi di `welcome.blade.php`, antara
        // `name="description"` dan `content="...">` -- membuat script
        // anti-FOUC berubah jadi teks yang ter-render di `<body>`:
        // 1039 karakter `(function () { var ACCENTS = ...` setinggi
        // 138px di paling atas halaman, dan `data-accent` tidak pernah
        // terpasang. Pemeriksaan kontras tetap hijau karena yang
        // diukur atributnya, bukan DOM-nya.
        $this->assertNoTagInsideTag($html, $name);

        file_put_contents(base_path(self::OUT.'/'.$name.'.html'), $html);
    }

    /**
     * Pastikan tidak ada tag baru yang dibuka sebelum tag sebelumnya
     * ditutup.
     *
     * Kalau terjadi, tag yang belum tertutup itu menelan seluruh
     * isi di depannya -- di `<meta name="description"` yang begitu,
     * browser memindahkan isi `<script>` ke `<body>` dan merendernya
     * sebagai teks yang terlihat.
     *
     * Yang diperiksa adalah HTML HASIL render, bukan sumbernya, jadi
     * kelas kesalahan yang sama di view mana pun ikut tertangkap.
     *
     * `DOMDocument` sengaja tidak dipakai: parser libxml tidak
     * mereproduksi quirks HTML5 yang sama, sehingga di situ skripnya
     * tetap terlihat "di dalam" tag `<meta>` dan bug-nya jadi tak
     * terdeteksi.
     *
     * Dua hal yang harus dilewati, karena `<` di sana cuma teks biasa:
     * isi `<style>`/`<script>`, dan `<` yang tidak diikuti huruf --
     * `Sistem Pelaporan <br>` di landing page, misalnya. Kombinasi
     * keduanya pernah bikin versi pertama guard ini melaporkan
     * false positive pada dua tempat.
     */
    private function assertNoTagInsideTag(string $html, string $name): void
    {
        $len = strlen($html);
        $i = 0;
        $blocks = 0;

        // Posisi `<` pembuka tag yang belum ditutup, atau null kalau
        // kita sedang di luar tag mana pun.
        $openAt = null;

        while ($i < $len) {
            $c = $html[$i];

            if ($c === '>') {
                $openAt = null;
                $i++;
                continue;
            }

            if ($c !== '<') {
                $i++;
                continue;
            }

            $startsTag = $i + 1 < $len && ctype_alpha($html[$i + 1]);

            if ($openAt !== null) {
                if ($startsTag) {
                    $ctx = rtrim(substr($html, 0, $openAt));

                    $this->fail(
                        'Halaman '.$name.': tag baru dibuka di posisi '.$i.' sementara tag '
                        .'yang dimulai di posisi '.$openAt.' belum ditutup -- konteks '
                        .'"'.substr($ctx, -60).'". Browser akan merender isi berikutnya '
                        .'sebagai teks. Periksa tag yang terpotong sebelum @include/@stack.'
                    );
                }
                $i++;
                continue;
            }

            if ($startsTag && preg_match('/^<(script|style)\b/i', substr($html, $i, 12), $m)) {
                $tag = strtolower($m[1]);
                $blocks++;
                $close = stripos($html, '</'.$tag, $i);
                $end = $close === false ? false : strpos($html, '>', $close);
                $i = ($end === false) ? $len : $end + 1;
                continue;
            }

            if ($startsTag) {
                $openAt = $i;
            }
            $i++;
        }

        // Supaya guard ini kelihatan benar-benar jalan. Kalau hanya
        // `fail()` tanpa penghitung, versi yang tidak memindai
        // apa pun ikut dilaporkan hijau, persis seperti guard yang
        // terlalu longgar -- dua-duanya sama-sama tidak berguna.
        $this->assertGreaterThan(
            0,
            $blocks,
            'Halaman '.$name.': tidak ada blok <script>/<style> yang dipindai, '
            .'jadi guard tag-nested yang longgar ikut dilaporkan hijau.'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        // `Tests\TestCase` memanggil `withoutVite()` supaya test biasa
        // tidak butuh `public/build/manifest.json`. Test ini justru
        // butuh CSS asli aplikasi -- kalau tidak, tag `@vite` hilang
        // dan audit berjalan di atas halaman tanpa tema sama sekali.
        $this->withVite();
    }

    public function test_dump_semua_halaman(): void
    {
        $dir = base_path(self::OUT);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        foreach (glob($dir.'/*.html') ?: [] as $old) {
            unlink($old);
        }

        // Publik
        foreach ([
            'publik-beranda' => '/',
            'publik-login' => '/login',
            'publik-login-admin' => '/login/admin',
            'publik-login-superadmin' => '/login/superadmin',
            'publik-register' => '/register',
            // Semua halaman auth pakai `layouts/guest`, jadi keduanya
            // juga terkunci light. Sebelumnya keduanya tidak ikut
            // diaudit, dan halaman lupa kata sandi tidak pernah
            // sampai ke layar -- artinya tidak pernah ikut diuji
            // kontrasnya di mode apa pun.
            'publik-lupa-password' => '/forgot-password',
            'publik-reset-password' => '/reset-password/token-audit',
        ] as $name => $url) {
            $this->dump($name, $url);
        }

        // Pelapor
        $this->isiDataUji();
        // Diambil SESUDAH data uji dibuat, kalau tidak keduanya null.
        $faq = TabelFaq::first();
        $laporan = Report::first();

        $this->post('/login', [
            'email' => 'pelapor@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        foreach ([
            'pelapor-dashboard' => '/dashboard',
            'pelapor-buat-laporan' => '/report',
            'pelapor-riwayat' => '/my-report',
            'pelapor-prosedur' => '/prosedur',
            'pelapor-pusat-bantuan' => '/pusat-bantuan',
            'pelapor-profil' => '/profile',
        ] as $name => $url) {
            $this->dump($name, $url);
        }

        // Admin
        $this->post('/logout');
        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
        foreach ([
            'admin-dashboard' => '/admin/dashboard',
            'admin-laporan' => '/admin/laporan',
            'admin-riwayat-status' => '/admin/laporan/riwayat-status',
            'admin-update-status' => '/admin/laporan/update-status',
            'admin-laporan-detail' => '/admin/laporan/'.$laporan->id,
            'admin-faq' => '/admin/manajemen-faq',
            'admin-faq-create' => '/admin/manajemen-faq/create',
            'admin-faq-edit' => '/admin/manajemen-faq/'.$faq->id_faq.'/edit',
            'admin-statistik' => '/admin/statistik',
            'admin-profil' => '/admin/profile',
        ] as $name => $url) {
            $this->dump($name, $url);
        }

        // Super admin
        $this->post('/logout');
        $this->post('/login/superadmin', [
            'email' => 'superadmin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('superadmin.dashboard'));
        foreach ([
            'super-dashboard' => '/superadmin/dashboard',
            'super-akun' => '/superadmin/accounts',
            'super-akun-create' => '/superadmin/accounts/create',
            'super-akun-edit' => '/superadmin/accounts/'.User::where('role', 'pelapor')->first()->id.'/edit',
            // Tidak ada halaman profil untuk super admin: route
            // `profile.edit` ada di dalam group middleware pelapor saja,
            // dan layout super admin memang tidak menautkan ke mana pun.
        ] as $name => $url) {
            $this->dump($name, $url);
        }

        $files = glob($dir.'/*.html');
        $this->assertNotEmpty($files, 'Tidak ada halaman yang tertulis.');
        fwrite(STDERR, "\n  tema: ".\count($files)." halaman ditulis ke "
            .self::OUT."\n");
    }
}
