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

        file_put_contents(base_path(self::OUT.'/'.$name.'.html'), $html);
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
