<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman auth harus selalu light, untuk semua role, tanpa menghapus
 * preferensi mode milik pengunjung.
 *
 * Tiga aturan yang dijaga test ini:
 *
 *   1. `layouts/guest.blade.php` menaruh `data-mode-locked="light"` di
 *      tag `<html>`, jadi kuncinya ada SEBELUM CSS pertama dimuat.
 *      Kalau atribut ini dipasang lewat JavaScript, halaman sempat
 *      ter-render memakai mode gelap yang tersimpan lalu berubah --
 *      kedip yang tidak akan ketahuan oleh audit mana pun.
 *
 *   2. Yang terkunci hanya `data-mode-resolved`. `data-mode` tetap
 *      menyimpan preferensi asli, dan skrip anti-FOUC tidak pernah
 *      menulis ke `localStorage` -- jadi dark mode tidak hilang begitu
 *      pengunjung lewat di layar auth.
 *
 *   3. Halaman di luar auth tidak ikut terkunci, dan pemilih
 *      Light/Dark/Sistem tetap ada di sana.
 *
 * Test memeriksa markup yang benar-benar dirender, bukan isi
 * `localStorage` -- tidak ada browser di sini. Perilaku runtime-nya
 * diukur `tools/audit_theme.py`, yang sekarang memaksa
 * `data-mode-resolved` ke mode terkunci untuk semua kombinasi.
 */
class GuestLightModeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman yang memakai `x-guest-layout`.
     *
     * Daftar per-route, bukan per-layout: kalau suatu hari ada halaman
     * auth baru yang lupa memakai layout itu, halaman itu tidak akan
     * terkunci dan test ini tetap hijau -- kecuali route-nya ikut
     * ditambah di sini.
     */
    private const HALAMAN_AUTH = [
        '/login',
        '/login/admin',
        '/login/superadmin',
        '/register',
        '/forgot-password',
        '/reset-password/token-uji',
    ];

    /**
     * Apakah tag `<html>` halaman ini mengunci mode.
     *
     * Yang diperiksa atributnya, bukan teks `data-mode-locked` di
     * mana saja: partial `theme-bootstrap` menyebut nama atribut itu
     * di kode JS-nya dan dirender di SETIAP halaman, jadi pencarian
     * teks biasa akan ikut cocok di landing page.
     */
    private function terkunciLight(string $html): bool
    {
        return (bool) preg_match('/<html\b[^>]*\bdata-mode-locked="light"/i', $html);
    }

    public function test_setiap_halaman_auth_terkunci_light(): void
    {
        foreach (self::HALAMAN_AUTH as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertTrue(
                $this->terkunciLight($html),
                $url.' tidak mengunci mode light, jadi dark mode yang tersimpan '
                .'akan merender halaman auth ini gelap.'
            );

            // Penanda mode disembunyikan: memilih "Dark" di halaman
            // yang terkunci light tidak mengubah apa pun, jadi
            // menampilkannya hanya menawarkan pilihan yang bohong.
            $this->assertStringNotContainsString(
                'mode-seg',
                $html,
                $url.' masih menampilkan tombol Light/Dark/Sistem padahal '
                .'modenya terkunci.'
            );

            // Swatch aksen TETAP ada: mengganti warna aksen tidak
            // menyentuh mode, jadi itu satu-satunya pengaturan tema
            // yang masih berarti di halaman ini.
            $this->assertSame(
                6,
                substr_count($html, 'class="swatch sw-'),
                $url.' kehilangan swatch aksen.'
            );
        }
    }

    /**
     * Dua halaman auth ini memakai `x-guest-layout` tetapi berada di
     * balik middleware `auth`, jadi tidak bisa diuji sebagai tamu.
     * Kunci mereka tetap ikut diuji karena keduanya dirender lewat
     * layout yang sama.
     */
    public function test_halaman_auth_yang_memerlukan_login_ikut_terkunci(): void
    {
        User::factory()->create([
            'email' => 'pelapor@serojap.test',
            'role' => 'pelapor',
            // `verify-email` dialihkan ke dashboard kalau alamatnya
            // sudah terverifikasi, jadi pengguna ini harus belum.
            'email_verified_at' => null,
        ]);

        $this->post('/login', [
            'email' => 'pelapor@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        foreach (['/verify-email', '/confirm-password'] as $url) {
            $this->assertTrue(
                $this->terkunciLight($this->get($url)->assertOk()->getContent()),
                $url.' tidak mengunci mode light.'
            );
        }
    }

    /**
     * Halaman di luar auth TIDAK boleh ikut terkunci.
     *
     * Ini penjaga dari kesalahan yang paling mungkin terjadi di
     * kemudian hari: `theme-bootstrap` diikutkan ke semua layout
     * tanpa kecuali, atau atributnya ditempel di `body`.
     */
    public function test_halaman_non_auth_tidak_terkunci(): void
    {
        $beranda = $this->get('/')->assertOk()->getContent();

        $this->assertFalse(
            $this->terkunciLight($beranda),
            'Landing page ikut terkunci light; halaman itu harus bebas mode.'
        );
        $this->assertStringContainsString(
            'mode-seg',
            $beranda,
            'Landing page kehilangan pemilih Light/Dark/Sistem.'
        );

        User::factory()->create([
            'email' => 'admin@serojap.test',
            'role' => 'admin',
        ]);
        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertFalse(
            $this->terkunciLight($this->get('/admin/dashboard')->assertOk()->getContent()),
            'Dashboard admin ikut terkunci light; begitu login, mode asli '
            .'harus berlaku kembali.'
        );
    }

    /**
     * Skrip anti-FOUC hanya boleh MEMBACA `localStorage`.
     *
     * Inilah yang membuat preferensi dark mode selamat dari kunjungan ke
     * layar auth. Kalau suatu hari ada `setItem` di dalamnya, wajah
     * halamannya tetap benar dan test inilah yang lebih dulu gagal --
     * bukan pengguna yang diam-diam kehilangan preferensinya.
     */
    public function test_script_anti_fouc_tidak_menulis_preferensi(): void
    {
        $skrip = view('partials.theme-bootstrap')->render();

        $this->assertStringContainsString(
            "root.setAttribute('data-mode', mode)",
            $skrip,
            'Skrip anti-FOUC harus tetap menulis `data-mode` apa adanya '
            .'supaya preferensi asli masih terbaca.'
        );

        $this->assertStringNotContainsString(
            'setItem',
            $skrip,
            'Skrip anti-FOUC tidak boleh menulis ke localStorage: mengunci '
            .'light tidak boleh menimpa mode yang tersimpan.'
        );

        $this->assertStringContainsString(
            "root.getAttribute('data-mode-locked')",
            $skrip,
            'Skrip anti-FOUC harus menghormati kunci mode. Kalau tidak, '
            .'halaman auth sempat ter-render gelap sebelum CSS kedua '
            .'berlaku.'
        );
    }
}
