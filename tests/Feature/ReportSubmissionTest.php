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

        $response->assertRedirect(route('laporan.create'));
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
        $this->assertContains(
            'Ukuran foto maksimal 5120 kilobyte (5MB).',
            $this->semuaPesanValidasi()
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

        $errors = session('errors')->getBag('default')->all();

        $this->assertContains('Nama pelapor wajib diisi.', $errors);
        $this->assertContains(
            'Foto kerusakan jalan wajib diunggah.',
            $errors
        );
        $this->assertContains(
            'Keterangan kerusakan wajib diisi.',
            $errors
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

        $this->actingAs($admin)
            ->get(route('laporan.create'))
            ->assertRedirect(route('login'));
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
