<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Logo SEROJAP harus punya SATU sumber yang benar-benar sama di
 * setiap halaman.
 *
 * Kenapa test ini perlu ada
 * ------------------------
 * Dua logo berbeda pernah aktif bersamaan: lotus navy/teal di navbar
 * dashboard, dan inline SVG "benih" di auth + sidebar admin +
 * sidebar superadmin + footer. Keduanya digambar independen, jadi
 * bentuknya berbeda di halaman berbeda -- dan tidak ada satu pun test
 * yang gagal, karena tiap panggilannya "benar" menurut berkas-nya sendiri.
 *
 * Yang dijaga di sini:
 *
 *   1. Setiap halaman yang punya logo merender `<img>` dengan `src`
 *      yang persis sama dan hanya satu, di dalam `.serojap-logo`.
 *      Jumlah logo per halaman dikunci, supaya halaman tidak diam-diam
 *      memuat logo kedua dari sumber lain.
 *
 *   2. Tidak ada view yang boleh menunjuk berkas logo secara
 *      langsung. Ini penjaga utama: begitu ada `<img src="...logo
 *      .png">` lagi di view mana pun, halaman itu akan menampilkan
 *      bentuk yang berbeda dari yang lain.
 *
 *   3. `favicon.ico` tidak boleh 0 byte dan harus dirujuk lewat
 *      `partials.favicon`. Duluan keduanya benar: berkasnya kosong
 *      dan tidak ada tag-nya di satu pun halaman.
 *
 * Test memeriksa markup yang dirender, bukan piksel. Bentuk artwork
 * diuji terpisah olehIoU mask (lihat catatan di komponen `<x-logo>`);
 * di sini yang dijaga adalah "sumbernya sama", bukan "gambarnya
 * benar".
 */
class LogoConsistencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Aset logo resmi. Hanya boleh ada satu, dan hanya boleh dirujuk
     * lewat komponen `<x-logo>`.
     */
    private const ASET_LOGO = 'assets/pelapor/images/logo-serojap.webp';

    /**
     * Nama view yang boleh memuat logo. Dipakai oleh test kedua, yang
     * menyisir seluruh `resources/views`.
     */
    private const VIEW_YANG_BOLEH = 'components/logo.blade.php';

    /**
     * Halaman yang harus punya logo, dikelompokkan per layout supaya
     * jelas mana yang diuji sebagai tamu dan mana sebagai pengguna
     * yang sudah masuk.
     *
     * Daftar per halaman, bukan per layout: kalau nanti ada halaman
     * baru di `layouts/app` yang tidak sengaja tidak memakai
     * `<x-logo>`, test ini hanya bisa menangkapnya kalau halamannya
     * ikut ditambah di sini.
     *
     * @return array<string, array<int, string>>
     */
    private function halamanTamu(): array
    {
        return [
            'auth login pelapor' => ['/login'],
            'auth login admin' => ['/login/admin'],
            'auth login superadmin' => ['/login/superadmin'],
            'auth register' => ['/register'],
            'auth lupa password' => ['/forgot-password'],
            'auth reset password' => ['/reset-password/token-uji'],
        ];
    }

    /**
     * Cek satu halaman: logo ada, satu, dan dari sumber yang benar.
     */
    private function assertLogoTunggal(string $label, string $html, int $harus = 1): void
    {
        $jumlah = substr_count($html, 'class="serojap-logo"');

        $this->assertSame(
            $harus,
            $jumlah,
            $label.' harus merender tepat '.$harus.' logo, found '.$jumlah.'. '
            .'Logo kedua berarti bentuk yang berbeda masuk ke halaman ini.'
        );

        if ($harus === 0) {
            return;
        }

        $this->assertStringContainsString(
            self::ASET_LOGO,
            $html,
            $label.' tidak memuat aset logo resmi ('.self::ASET_LOGO.').'
        );

        // `<img>` logo harus hidup di dalam `.serojap-logo`, karena
        // plat-nya yang membuat kontrasnya bisa diukur. `<img>` telanjang
        // di atas kartu putih lolos cek `src` tapi kontrasnya hilang.
        // `\s+` antar token, bukan spasi: Blade menulis atribut komponen
        // satu per baris, jadi `<span class=` tidak pernah muncul
        // bersama di HTML hasil render.
        $this->assertMatchesRegularExpression(
            '#<span\s+class="serojap-logo"[^>]*>\s*<img[^>]*src="[^"]*'
                .preg_quote(self::ASET_LOGO, '#').'"#s',
            $html,
            $label.' memuat aset logo di luar pembungkus `.serojap-logo`, '
            .'jadi plat kontrasnya tidak ikut.'
        );
    }

    public function test_halaman_tamu_semua_pakai_sumber_logo_yang_sama(): void
    {
        foreach ($this->halamanTamu() as $label => $urls) {
            foreach ($urls as $url) {
                $this->assertLogoTunggal($label.' ('.$url.')', $this->get($url)->assertOk()->getContent());
            }
        }
    }

    /**
     * Landing page publik tidak punya navbar, jadi tidak punya logo.
     *
     * Dinyatakan eksplisit supaya status ini disengaja dan bukan
     * sekadar tidak terlihat. Kalau suatu saat landing page diberi
     * navbar, angka 0 di sini yang harus diubah -- bukan dibiarkan
     * begitu saja sementara halaman itu diam-diam memuat logo dari
     * sumber yang salah.
     */
    public function test_landing_page_tidak_punya_logo(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('serojap-logo', $html);
        $this->assertStringNotContainsString(
            'logo-serojap',
            $html,
            'Landing page memuat logo dari sumber lain. Kalau memang '
            .'mau menampilkannya, pakai komponen logo yang sama seperti '
            .'halaman lain.'
        );
    }

    public function test_navbar_dan_kartu_laporan_pakai_sumber_logo_yang_sama(): void
    {
        User::factory()->create([
            'email' => 'pelapor@serojap.test',
            'role' => 'pelapor',
        ]);
        $this->post('/login', [
            'email' => 'pelapor@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        // Navbar pelapor + footer.
        $dashboard = $this->get('/dashboard')->assertOk()->getContent();
        $this->assertLogoTunggal('dashboard pelapor', $dashboard, 2);

        // Header form laporan. Pakai nama route, bukan path: path-nya
        // `/report` dan tidak mengikuti pola `/laporan/...` yang dipakai
        // halaman lain, jadi mudah salah ketik.
        //
        // Tiga, bukan satu: halaman ini juga memakai `layouts/app`, jadi
        // navbar dan footernya ikut. Ketiganya harus dari sumber yang
        // sama -- itulah yang diuji, bukan jumlahnya.
        $form = $this->get(route('laporan.create'))->assertOk()->getContent();
        $this->assertLogoTunggal('form laporan', $form, 3);
    }

    public function test_sidebar_admin_dan_superadmin_pakai_sumber_logo_yang_sama(): void
    {
        User::factory()->create(['email' => 'admin@serojap.test', 'role' => 'admin']);
        $this->post('/login/admin', [
            'email' => 'admin@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertLogoTunggal('dashboard admin', $this->get('/admin/dashboard')->assertOk()->getContent(), 1);

        $this->post('/logout');

        User::factory()->create(['email' => 'super@serojap.test', 'role' => 'super_admin']);
        $this->post('/login/superadmin', [
            'email' => 'super@serojap.test',
            'password' => 'password',
        ])->assertRedirect(route('superadmin.dashboard'));
        $this->assertLogoTunggal(
            'dashboard superadmin',
            $this->get('/superadmin/dashboard')->assertOk()->getContent(),
            1
        );
    }

    /**
     * Penjaga utama: TIDAK ADA view yang menunjuk berkas logo langsung.
     *
     * Inilah yang membuat logo bisaandersimpang tanpa ketahuan. Begitu
     * ada view yang menulis `<img src="{{ asset('logo.png') }}">`
     * sendiri, halaman itu displaying bentuk yang berbeda, dan tidak
     * ada yang gagal.
     */
    public function test_tidak_ada_view_yang_merujuk_berkas_logo_langsung(): void
    {
        $ditemukan = [];

        $viewFiles = $this->viewFiles();
        $this->assertGreaterThan(
            40,
            count($viewFiles),
            'Pemindaian view tidak menemukan berkas apa pun. Kalau guard ini '
            .'hijau karena tidak memindai apa-apa, ia tidak berguna sama sekali.'
        );

        foreach ($viewFiles as $path) {
            // Selalu garis miring, bukan DIRECTORY_SEPARATOR: kunci
            // hasil scan dibandingkan dengan `VIEW_YANG_BOLEH` yang
            // ditulis dengan garis miring. Di Windows separator-nya
            // `\`, jadi `unset()` tidak akan pernah mengenai dan
            // komponen logo sendiri ikut dilaporkan sebagai pelanggaran.
            $relatif = str_replace('\\', '/', $path);
            $relatif = preg_replace('#^.*?/resources/views/#', '', $relatif);
            $isi = (string) file_get_contents($path);

            // Nama berkas logo yang pernah bermasalah, plus pola umum
            // supaya aset baru tidak bisa menyusup diam-diam.
            $pola = '/(logo[-_.]?[a-z0-9]*\.(svg|png|webp|jpg|jpeg|ico))/i';

            if (! preg_match_all($pola, $isi, $cocok)) {
                continue;
            }

            foreach ($cocok[1] as $nama) {
                $ditemukan[$relatif] = array_merge($ditemukan[$relatif] ?? [], [$nama]);
            }
        }

        // Komponen `<x-logo>` boleh menyebut nama berkasnya; view lain
        // tidak boleh. Komponen juga boleh menjelaskan asal-usulnya di
        // komentar, jadi yang diperiksa hanya pemakaian di luar
        // komponen.
        unset($ditemukan[self::VIEW_YANG_BOLEH]);

        $this->assertSame(
            [],
            $ditemukan,
            "View berikut menyebut berkas logo secara langsung. Semua logo harus lewat "
            .'<x-logo>, kalau tidak bentuknya bisa berbeda lagi antar halaman: '
            .json_encode($ditemukan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Komponen tunggalnya harus benar-benar memuat aset resmi.
     *
     * Tanpa ini, `test_tidak_ada_view_yang_merujuk_berkas_logo_langsung`
     * bisa hijau saat logo-nya hilang dari halaman mana pun.
     */
    public function test_komponen_logo_memuat_aset_resmi(): void
    {
        //Dibaca dari hasil render, bukan isi berkas: `class="serojap-logo"`
        //datang dari `$attributes->merge()`, jadi teks itu tidak pernah
        //muncul di source dan akan selalu gagal kalau dicari di sana.
        $komponen = (string) view('components.logo', ['size' => 40])->render();

        $this->assertStringContainsString(
            self::ASET_LOGO,
            $komponen,
            'Komponen <x-logo> harus memuat aset logo resmi.'
        );

        $this->assertMatchesRegularExpression(
            '/class="[^"]*\bserojap-logo\b/',
            $komponen,
            'Pembungkus logo harus memakai kelas `serojap-logo` yang di-style di theme.css.'
        );
    }

    /**
     * Favicon: berkasnya tidak boleh kosong, dan harus dirujuk.
     *
     * `favicon.ico` pernah 0 byte tanpa satu pun tag `<link>` di situs
     * ini. Browser tetap meminta berkas itu, mendapat jawaban kosong,
     * dan tidak menampilkan ikon. Karena tidak ada kode yang salah,
     * tidak ada yang bisa diperbaiki -- sekarang keduanya dijaga.
     */
    public function test_favicon_ada_berkas_dan_ditautkan(): void
    {
        $jalur = public_path('favicon.ico');

        $this->assertFileExists($jalur);
        $this->assertGreaterThan(
            0,
            filesize($jalur),
            'public/favicon.ico kosong, jadi browser tidak menampilkan ikon apa pun.'
        );

        foreach ($this->halamanTamu() as $label => $urls) {
            foreach ($urls as $url) {
                $html = $this->get($url)->assertOk()->getContent();
                $this->assertStringContainsString(
                    'rel="icon"',
                    $html,
                    $label.' ('.$url.') tidak menautkan favicon.'
                );
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function viewFiles(): array
    {
        $berkas = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            // `getExtension()` hanya mengembalikan ekstensi TERAKHIR,
            // jadi untuk `app.blade.php` hasilnya `php`, bukan
            // `blade.php`. Dipakai perbandingan itu, `viewFiles()`
            // mengembalikan nol berkas dan seluruh test di bawahnya
            // hijau tanpa memindai apa pun -- persis kelas bug yang
            // test ini dibuat untuk dicegah.
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $berkas[] = $file->getPathname();
            }
        }

        sort($berkas);

        return $berkas;
    }
}
