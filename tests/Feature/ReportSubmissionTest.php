<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MembuatFileGambar;
use Tests\TestCase;

class ReportSubmissionTest extends TestCase
{
    use RefreshDatabase;
    use MembuatFileGambar;

    /**
     * Data laporan yang valid.
     */
    private function dataLaporan(array $override = []): array
    {
        return array_merge([
            'nama' => 'Budi Santoso',
            'foto' => $this->fileGambar('jalan.jpg'),
            'alamat' => 'Jl. Rayacibadak, Kp. Sukamaju, Baca, Purwakarta',
            'latitude' => '-6.55800000',
            'longitude' => '107.76000000',
            'keterangan' => 'Jalan berlubang cukup lebar sehingga berbahaya untuk kendaraan bermotor.',
        ], $override);
    }

    public function test_pelapor_yang_sudah_login_bisa_submit_laporan_baru(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->from(route('laporan.create'))
            ->post(route('laporan.store'), $this->dataLaporan());

        // Setelah sukses, pengguna diarahkan ke Riwayat Laporan supaya
        // bisa langsung memantau nomor referensinya.
        $response->assertRedirect(route('laporan.my-report'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('reports', 1);

        $laporan = Report::first();

        $this->assertSame($pelapor->id, $laporan->user_id);
        $this->assertSame('Budi Santoso', $laporan->nama_pelapor);
        $this->assertNotNull($laporan->foto);
        $this->assertSame(
            'Jl. Rayacibadak, Kp. Sukamaju, Baca, Purwakarta',
            $laporan->alamat
        );

        Storage::disk('public')->assertExists($laporan->foto);
    }

    public function test_status_awal_diterima_dibuat_otomatis(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan());

        $laporan = Report::first();

        $this->assertDatabaseHas('tabel_status', [
            'report_id' => $laporan->id,
            'user_id' => $pelapor->id,
            'status' => 'diterima',
        ]);

        $this->assertSame('diterima', $laporan->latestStatus->status);
        $this->assertCount(1, $laporan->statuses);
    }

    public function test_submit_tanpa_foto_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $data = $this->dataLaporan();
        unset($data['foto']);

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $data);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_submit_dengan_file_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'foto' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ]));

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_foto_lebih_dari_lima_megabyte_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'foto' => $this->fileGambarSebesar(6000, 'jalan-besar.jpg'),
            ]));

        $response->assertSessionHasErrors('foto');

        // Pesannya boleh berubah kalimatnya, tapi wajib menyebut batas 5 MB
        // supaya pengguna paham kenapa foto ditolak.
        $this->assertNotEmpty(
            array_filter(
                $this->semuaPesanValidasi(),
                fn (string $pesan): bool => str_contains($pesan, '5 MB')
            ),
            'Pesan validasi foto harus menyebut batas 5 MB. Pesan aktual: '
                .implode(' | ', $this->semuaPesanValidasi())
        );

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_koordinat_bukan_angka_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'latitude' => 'bukan-angka',
                'longitude' => 'bukan-angka',
            ]));

        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_koordinat_di_luar_rentang_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'latitude' => '120',
                'longitude' => '220',
            ]));

        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_keterangan_terlalu_pendek_ditolak(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'keterangan' => 'lubang',
            ]));

        $response->assertSessionHasErrors('keterangan');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_pesan_validasi_berbahasa_indonesia(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $response = $this->actingAs($pelapor)
            ->post(route('laporan.store'), []);

        $response->assertSessionHasErrors([
            'nama',
            'foto',
            'alamat',
            'latitude',
            'longitude',
            'keterangan',
        ]);

        // `MessageBag::all()` mengembalikan daftar pesan yang diratakan,
        // jadi untuk lookup per-field harus pakai `getMessages()`.
        $errors = session('errors')->getBag('default')->getMessages();

        $wajib = ['nama', 'foto', 'alamat', 'latitude', 'longitude', 'keterangan'];

        foreach ($wajib as $field) {
            $this->assertNotEmpty(
                $errors[$field] ?? [],
                "Field {$field} harus punya pesan validasi."
            );
        }

        // Yang diuji di sini adalah bahasanya, bukan kalimat persisnya:
        // pesan boleh diubah kalimatnya, tapi tidak boleh jatuh ke bawaan
        // Laravel yang berbahasa Inggris.
        $polaInggris = [
            'field is required',
            'must be an image',
            'may not be greater than',
            'must be a number',
            'at least',
        ];

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $this->assertNotEmpty(trim($message), "Pesan untuk {$field} tidak boleh kosong.");

                foreach ($polaInggris as $pola) {
                    $this->assertStringNotContainsStringIgnoringCase(
                        $pola,
                        $message,
                        "Pesan untuk {$field} masih bahasa Inggris: {$message}"
                    );
                }
            }
        }

        // Nama custom attribute harus terpakai, supaya yang tampil
        // "nama pelapor", bukan "nama".
        $this->assertStringContainsString(
            'nama pelapor',
            strtolower($errors['nama'][0] ?? '')
        );
    }

    public function test_guest_tidak_bisa_mengakses_halaman_laporan(): void
    {
        $this->get(route('laporan.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_tidak_bisa_submit_laporan(): void
    {
        Storage::fake('public');

        $this->post(route('laporan.store'), $this->dataLaporan())
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_admin_tidak_bisa_mengakses_halaman_laporan_pelapor(): void
    {
        $admin = User::factory()->admin()->create();

        // `PelaporMiddleware` mengarahkan admin ke dashboard admin-nya
        // sendiri, bukan logout.
        $response = $this->actingAs($admin)
            ->get(route('laporan.create'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * =================================================================
     * TASK A - PEMBUKTIAN DESAIN RATE LIMIT 2 LAPIS
     *
     * Rate limit submit laporan sengaja dibuat dua lapis:
     *
     *   1. `throttle:20,1` sebagai middleware di route -> pagar luar,
     *      menahan request mentah sebanyak apa pun.
     *   2. Limiter manual 5/menit di controller -> baru dihitung
     *      SETELAH validasi lolos, jadi laporan yang BENAR-BENAR
     *      terkirim saja yang dihitung.
     *
     * Test di bawah mengunci kedua lapis itu. Tanpa test ini, refactor
     * berikutnya bisa dengan sengaja atau tidak mengembalikan
     * `throttle` ke posisi lama (sebelum controller) dan tidak ada yang
     * sadar, karena user yang salah upload foto lalu kena 429 tanpa
     * tahu kenapa.
     * =================================================================
     */

    public function test_percobaan_gagal_validasi_tidak_menghitung_limiter(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        // 7 kali: cukup melewati batas 5/menit kalau limiter ikut menghitung
        // request yang gagal validasi.
        for ($i = 1; $i <= 7; $i++) {
            $response = $this->actingAs($pelapor)
                ->post(route('laporan.store'), [
                    'nama' => 'Budi Santoso',
                    'foto' => $this->fileGambar('jalan.jpg'),
                    'alamat' => 'Jl. Raya Purwakarta',
                    'latitude' => '-6.558',
                    'longitude' => '107.760',
                    // Terlalu pendek -> gagal validasi `min:10`.
                    'keterangan' => 'lubang',
                ]);

            $this->assertNotSame(
                429,
                $response->getStatusCode(),
                "Percobaan ke-{$i} tidak boleh kena rate limit: validasi gagal "
                    .'duluan, jadi belum ada laporan yang terkirim.'
            );

            $response->assertSessionHasErrors('keterangan');
        }

        $this->assertDatabaseCount('reports', 0);

        // Kalau limiter tidak ikut menghitung request gagal, attempt
        // ke-6 (yang valid) masih harus lolos -- ini bukti langsung
        // bahwa kuota tidak terpakai oleh 7 percobaan gagal di atas.
        $berhasil = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan());

        $berhasil->assertRedirect(route('laporan.my-report'));
        $this->assertDatabaseCount('reports', 1);
    }

    public function test_laporan_valid_ke_enam_kenai_limiter_manual_bukan_middleware(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->actingAs($pelapor)
                ->post(route('laporan.store'), $this->dataLaporan([
                    'keterangan' => 'Laporan ke-' . $i . ', jalan berlubang berbahaya.',
                ]));

            $this->assertNotSame(429, $response->getStatusCode(), "Laporan ke-{$i} seharusnya lolos.");
        }

        // Laporan ke-6: kena limiter manual, BUKAN `throttle` middleware.
        $keenam = $this->actingAs($pelapor)
            ->post(route('laporan.store'), $this->dataLaporan([
                'keterangan' => 'Laporan ke-6, jalan berlubang berbahaya.',
            ]));

        $keenam->assertStatus(429);

        // Penanda limiter manual: pesannya Bahasa Indonesia dan menyebut
        // jumlah detik menunggu. 429 dari middleware `throttle` bawaan
        // Laravel selalu bertuliskan "Too Many Attempts." dan sengaja
        // disembunyikan oleh resources/views/errors/429.blade.php.
        $keenam->assertSee('Terlalu banyak laporan dikirim dari perangkat ini');
        $keenam->assertDontSee('Too Many Attempts.');

        $this->assertDatabaseCount('reports', 5);
    }

    public function test_throttle_route_tetap_jalan_sebagai_pagar_luar(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        // Data sengaja dibuat INVALID supaya limiter manual tidak pernah
        // tersentuh. Dengan begitu 20 request ini murni dihitung
        // middleware `throttle:20,1` di route.
        $kirimInvalid = function () use ($pelapor) {
            return $this->actingAs($pelapor)
                ->post(route('laporan.store'), [
                    'nama' => 'Budi Santoso',
                    'foto' => $this->fileGambar('jalan.jpg'),
                    'alamat' => 'Jl. Raya Purwakarta',
                    'latitude' => '-6.558',
                    'longitude' => '107.760',
                    'keterangan' => 'lubang',
                ]);
        };

        for ($i = 1; $i <= 20; $i++) {
            $response = $kirimInvalid();

            $this->assertNotSame(
                429,
                $response->getStatusCode(),
                "Request ke-{$i} masih di bawah pagar luar 20/menit."
            );
        }

        // Request ke-21 kena middleware `throttle` -> 429.
        $response = $kirimInvalid();
        $response->assertStatus(429);

        // 429 middleware tidak boleh menampilkan pesan Bahasa Indonesia
        // milik limiter manual, karena itu datang dari tempat lain.
        $response->assertDontSee('Terlalu banyak laporan dikirim dari perangkat ini');
        $response->assertSee('Terlalu banyak permintaan');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_submit_laporan_dibatasi_rate_limit(): void
    {
        Storage::fake('public');

        $pelapor = User::factory()->pelapor()->create();

        $terkirim = 0;

        for ($i = 0; $i < 6; $i++) {
            $response = $this->actingAs($pelapor)
                ->post(route('laporan.store'), $this->dataLaporan([
                    'keterangan' => 'Laporan vandal ke-' . ($i + 1) . ' pada jalan raya.',
                ]));

            if ($response->getStatusCode() === 429) {
                break;
            }

            $terkirim++;
        }

        $this->assertSame(5, $terkirim, 'Hanya 5 laporan yang boleh terkirim per menit.');
        $this->assertDatabaseCount('reports', 5);
    }

    public function test_riwayat_laporan_hanya_menampilkan_laporan_sendiri(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $pelaporLain = User::factory()->pelapor()->create();

        $laporanSendiri = Report::factory()->for($pelapor)->create();
        $laporanOrangLain = Report::factory()->for($pelaporLain)->create();

        TabelStatus::factory()
            ->for($laporanSendiri, 'laporan')
            ->create(['status' => 'diterima']);

        TabelStatus::factory()
            ->for($laporanOrangLain, 'laporan')
            ->create(['status' => 'diterima']);

        $response = $this->actingAs($pelapor)
            ->get(route('laporan.my-report'));

        $response->assertOk();
        $response->assertSee($laporanSendiri->alamat);
        $response->assertDontSee($laporanOrangLain->alamat);
    }

    /**
     * Semua pesan validasi yang terkumpul pada request terakhir.
     */
    private function semuaPesanValidasi(): array
    {
        return session('errors')->getBag('default')->all();
    }
}
