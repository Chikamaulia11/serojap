<?php

namespace Tests\Feature;

use App\Models\TabelFaq;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Menjamin setiap halaman aplikasi benar-benar bisa dirender.
 *
 * Test ini sengaja TIDAK memakai withoutVite() supaya manifest Vite ikut
 * diverifikasi. Kalau ada view yang hilang, route salah, atau @vite gagal,
 * test ini akan gagal.
 */
class RuntimeSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function buatDemo(): void
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
    }

    public function test_halaman_publik_dapat_dirender(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/login/admin')->assertOk();
        $this->get('/login/superadmin')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_semua_halaman_pelapor_dapat_dirender(): void
    {
        $this->buatDemo();
        TabelFaq::create([
            'user_id' => 1,
            'pertanyaan' => 'Bagaimana cara melapor?',
            'jawaban' => 'Lewat menu Laporan.',
            'urutan' => 1,
        ]);

        $pelapor = User::where('email', 'pelapor@serojap.test')->first();

        $response = $this->post('/login', [
            'email' => 'pelapor@serojap.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($pelapor);

        foreach ([
            '/dashboard',
            '/report',
            '/my-report',
            '/pusat-bantuan',
            '/prosedur',
            '/profile',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_semua_halaman_admin_dapat_dirender(): void
    {
        $this->buatDemo();

        $laporan = \App\Models\Report::create([
            'user_id' => 1,
            'nama_pelapor' => 'Warga Trials',
            'alamat' => 'Jl. Uji Coba No. 1, Purwakarta',
            'latitude' => -6.55,
            'longitude' => 107.49,
            'keterangan' => 'Lubang untuk keperluan pengujian.',
        ]);

        TabelStatus::create([
            'report_id' => $laporan->id,
            'user_id' => 2,
            'status' => 'diterima',
            'keterangan' => 'Diterima untuk pengujian.',
        ]);

        $faq = TabelFaq::create([
            'user_id' => 2,
            'pertanyaan' => 'Pertanyaan uji',
            'jawaban' => 'Jawaban uji.',
            'urutan' => 1,
        ]);

        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        foreach ([
            '/admin/dashboard',
            '/admin/laporan',
            '/admin/laporan/riwayat-status',
            '/admin/laporan/update-status',
            '/admin/laporan/'.$laporan->id,
            '/admin/manajemen-faq',
            '/admin/manajemen-faq/create',
            '/admin/manajemen-faq/'.$faq->id_faq,
            '/admin/manajemen-faq/'.$faq->id_faq.'/edit',
            '/admin/statistik',
            '/admin/profile',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_semua_halaman_super_admin_dapat_dirender(): void
    {
        $this->buatDemo();

        $akun = User::where('email', 'admin@serojap.test')->first();

        $this->post('/login/superadmin', [
            'email' => 'superadmin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('superadmin.dashboard'));

        foreach ([
            '/superadmin/dashboard',
            '/superadmin/accounts',
            '/superadmin/accounts/create',
            '/superadmin/accounts/'.$akun->id.'/edit',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_role_tidak_bisa_membuka_halaman_role_lain(): void
    {
        $this->buatDemo();

        $this->post('/login', [
            'email' => 'pelapor@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get('/admin/laporan')->assertRedirect();
        $this->get('/superadmin/accounts')->assertRedirect();
    }

    public function test_halaman_404_menautkan_dashboard_sesuai_role(): void
    {
        $this->buatDemo();

        // Tamu: hanya ada jalan kembali ke beranda.
        $this->get('/tidak-ada-halaman-ini')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee(route('beranda'))
            ->assertDontSee('Ke Dashboard');

        // Endpoint login dipisah per role. Kalau semua_role dikirim ke
        // `/login`, request admin/superadmin tidak akan terautentikasi,
        // dan test ini akan salah menyimpulkan link 404-nya rusak.
        foreach ([
            ['/login', 'pelapor@serojap.test', 'dashboard'],
            ['/login/admin', 'admin@serojap.test', 'admin.dashboard'],
            ['/login/superadmin', 'superadmin@serojap.test', 'superadmin.dashboard'],
        ] as [$loginPath, $email, $routeName]) {
            $this->post($loginPath, [
                'email' => $email,
                'password' => 'password',
            ])->assertRedirect(route($routeName));

            $this->get('/tidak-ada-halaman-ini')
                ->assertNotFound()
                ->assertSee('Ke Dashboard')
                ->assertSee(route($routeName));

            $this->post('/logout');
        }
    }

    public function test_ganti_akun_di_403_logout_dulu_lalu_masuk_ke_login_sebelumnya(): void
    {
        // Halaman 403 tidak terjangkau lewat alur normal: akses
        // wrong-role justru dialihkan ke dashboard sendiri, bukan
        // `abort(403)`. Jadi test ini mendaftarkan route yang memaksa
        // 403, dengan middleware `web` supaya session hidup -- persis
        // kondisi yang membuat `auth()` di view terisi.
        Route::middleware('web')->get('/__uji-403', function () {
            abort(403);
        });

        $this->buatDemo();

        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $halaman = $this->get('/__uji-403');

        $halaman->assertForbidden();
        $halaman->assertSee('Akses ditolak');

        // Bentuknya harus form POST ke logout, bukan `<a>` ke halaman
        // login. Versi `<a>` itu bug-nya: `/login/admin` dipagari
        // middleware `guest`, jadi user yang masih login dipantul balik
        // ke dashboard-nya sendiri dan tombolnya tidak melakukan apa pun.
        $halaman->assertSee('action="'.e(route('logout')).'"', false);
        $halaman->assertSee('name="redirect" value="'.e(route('login.admin')).'"', false);
        $halaman->assertSee('<button type="submit"', false);

        $response = $this->post('/logout', [
            'redirect' => route('login.admin'),
        ]);

        // Bukan pantul balik: benar-benar logout, lalu mendarat di
        // halaman login area yang sama.
        $response->assertRedirect(route('login.admin'));
        $this->assertGuest('web');

        // Dan halaman tujuan benar-benar bisa dirender -- kalau masih
        // ada session sisa, middleware `guest` akan memantul lagi.
        $this->get('/login/admin')
            ->assertOk()
            ->assertSee('Administrator Serojap Purwakarta');
    }

    public function test_logout_menolak_tujuan_yang_bukan_halaman_login_milik_sendiri(): void
    {
        // `redirect` datang dari input pengguna. Kalau diteruskan apa
        // adanya, `POST /logout?redirect=https://phishing.example`
        // menjadi open redirect -- link logout milik aplikasi ini
        // sendiri bisa dipakai mengarahkan orang ke domain lain.
        $this->buatDemo();

        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ]);

        $this->post('/logout', [
            'redirect' => 'https://phishing.example/yml',
        ])->assertRedirect('/');

        $this->assertGuest('web');
    }
}
