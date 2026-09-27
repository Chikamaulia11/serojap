<?php

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MembuatFileGambar;
use Tests\TestCase;

class LaporanManagementTest extends TestCase
{
    use RefreshDatabase;
    use MembuatFileGambar;

    /**
     * Buat satu laporan beserta status awal `diterima`.
     */
    private function buatLaporan(?User $pelapor = null): Report
    {
        $laporan = Report::factory()
            ->for($pelapor ?? User::factory()->pelapor())
            ->create([
                'alamat' => 'Jl. Raya Wanaraja, Kp. Cibeureum, Wanaraja, Purwakarta',
            ]);

        TabelStatus::factory()
            ->for($laporan, 'laporan')
            ->create(['status' => 'diterima']);

        return $laporan;
    }

    public function test_admin_bisa_melihat_daftar_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertSee($laporan->alamat);
    }

    public function test_admin_bisa_mencari_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.index', ['search' => 'Wanaraja']));

        $response->assertOk();
        $response->assertSee($laporan->alamat);
    }

    public function test_admin_bisa_melihat_detail_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.show', $laporan->id));

        $response->assertOk();
        $response->assertSee($laporan->alamat);
        $response->assertSee($laporan->user->name);
    }

    public function test_admin_bisa_update_status_laporan(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $response = $this->actingAs($admin)
            ->put(route('admin.laporan.update', $laporan->id), [
                'status' => 'diproses',
                'keterangan' => 'Sudah masuk jadwal perbaikan tim lapangan.',
            ]);

        $response->assertRedirect(route('admin.laporan.show', $laporan->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tabel_status', [
            'report_id' => $laporan->id,
            'user_id' => $admin->id,
            'status' => 'diproses',
        ]);

        $this->assertSame('diproses', $laporan->fresh()->latestStatus->status);
        $this->assertCount(2, $laporan->fresh()->statuses);
    }

    public function test_admin_bisa_update_status_lengkap_dengan_foto_perbaikan(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $this->actingAs($admin)
            ->put(route('admin.laporan.update', $laporan->id), [
                'status' => 'selesai',
                'keterangan' => 'Jalan sudah ditambal dan kembali mulus.',
                'foto_perbaikan' => $this->fileGambar('perbaikan.jpg'),
            ])
            ->assertSessionHasNoErrors();

        $statusTerbaru = $laporan->fresh()->latestStatus;

        $this->assertSame('selesai', $statusTerbaru->status);
        $this->assertNotNull($statusTerbaru->foto_perbaikan);

        Storage::disk('public')->assertExists($statusTerbaru->foto_perbaikan);
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        $this->actingAs($admin)
            ->put(route('admin.laporan.update', $laporan->id), [
                'status' => 'entah',
                'keterangan' => 'Status tidak dikenal.',
            ])
            ->assertSessionHasErrors('status');

        $this->assertCount(1, $laporan->fresh()->statuses);
    }

    public function test_pelapor_tidak_bisa_mengakses_route_admin(): void
    {
        $pelapor = User::factory()->pelapor()->create();
        $laporan = $this->buatLaporan($pelapor);

        // Semua request dari role yang salah harus tertahan dan
        // diarahkan ke dashboard pelapor-nya sendiri.
        $this->actingAs($pelapor)
            ->get(route('admin.laporan.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pelapor)
            ->get(route('admin.laporan.show', $laporan->id))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pelapor)
            ->put(route('admin.laporan.update', $laporan->id), [
                'status' => 'selesai',
                'keterangan' => 'Mencoba update sendiri.',
            ])
            ->assertRedirect(route('dashboard'));

        // Role yang salah DITARUH di dashboard sendiri, tapi sesi tetap
        // dipakai: middleware sengaja tidak melakukan logout karena
        // orangnya cuma salah klik.
        $this->assertAuthenticatedAs($pelapor);

        // Tidak ada data yang berubah dari percobaan update tadi.
        $this->assertDatabaseMissing('tabel_status', [
            'report_id' => $laporan->id,
            'keterangan' => 'Mencoba update sendiri.',
        ]);
    }

    public function test_super_admin_diarahkan_ke_dashboard_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('admin.laporan.index'))
            ->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_guest_tidak_bisa_mengakses_daftar_laporan(): void
    {
        $this->get(route('admin.laporan.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_bisa_melihat_riwayat_status(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = $this->buatLaporan();

        TabelStatus::factory()
            ->for($laporan, 'laporan')
            ->create([
                'status' => 'diproses',
                'keterangan' => 'Tim sedang bekerja di lapangan.',
            ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.laporan.riwayat-status'));

        $response->assertOk();
        $response->assertSee('Tim sedang bekerja di lapangan.');
    }
}
