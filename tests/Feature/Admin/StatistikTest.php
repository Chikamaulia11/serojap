<?php

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatistikTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat laporan dengan status terakhir tertentu.
     *
     * Setiap pemanggilan membuat laporan baru beserta satu baris status.
     */
    private function laporanDenganStatus(string $status): Report
    {
        $laporan = Report::factory()->create();

        TabelStatus::factory()
            ->for($laporan, 'laporan')
            ->create(['status' => $status]);

        return $laporan;
    }

    public function test_admin_bisa_mengakses_halaman_statistik(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertOk();
    }

    public function test_pelapor_tidak_bisa_mengakses_halaman_statistik(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $this->actingAs($pelapor)
            ->get(route('admin.statistik.index'))
            ->assertRedirect(route('login.admin'));
    }

    public function test_statistik_kosong_saat_belum_ada_laporan(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertOk()
            ->assertViewHas('total', 0)
            ->assertViewHas('baru', 0)
            ->assertViewHas('diterima', 0)
            ->assertViewHas('proses', 0)
            ->assertViewHas('selesai', 0)
            ->assertViewHas('ditolak', 0);
    }

    public function test_angka_statistik_sesuai_data_yang_dis_seed(): void
    {
        $admin = User::factory()->admin()->create();

        // 2 diterima, 3 diproses, 4 selesai, 1 ditolak
        $this->laporanDenganStatus('diterima');
        $this->laporanDenganStatus('diterima');

        $this->laporanDenganStatus('diproses');
        $this->laporanDenganStatus('diproses');
        $this->laporanDenganStatus('diproses');

        $this->laporanDenganStatus('selesai');
        $this->laporanDenganStatus('selesai');
        $this->laporanDenganStatus('selesai');
        $this->laporanDenganStatus('selesai');

        $this->laporanDenganStatus('ditolak');

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertOk()
            ->assertViewHas('total', 10)
            ->assertViewHas('baru', 0)
            ->assertViewHas('diterima', 2)
            ->assertViewHas('proses', 3)
            ->assertViewHas('selesai', 4)
            ->assertViewHas('ditolak', 1);
    }

    public function test_hanya_status_terakhir_yang_dihitung(): void
    {
        $admin = User::factory()->admin()->create();

        // Laporan yang sudah selesai, lalu ditolak.
        $sudahSelesai = $this->laporanDenganStatus('selesai');
        TabelStatus::factory()
            ->for($sudahSelesai, 'laporan')
            ->create(['status' => 'ditolak']);

        // Laporan yang ditolak, lalu diproses lagi.
        $ditolakLaluProses = $this->laporanDenganStatus('ditolak');
        TabelStatus::factory()
            ->for($ditolakLaluProses, 'laporan')
            ->create(['status' => 'diproses']);

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertOk()
            ->assertViewHas('total', 2)
            ->assertViewHas('diterima', 0)
            ->assertViewHas('proses', 1)
            ->assertViewHas('selesai', 0)
            ->assertViewHas('ditolak', 1);
    }

    public function test_laporan_tanpa_status_dihitung_sebagai_baru(): void
    {
        $admin = User::factory()->admin()->create();

        Report::factory()->count(2)->create();
        $this->laporanDenganStatus('diterima');

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertOk()
            ->assertViewHas('total', 3)
            ->assertViewHas('baru', 2)
            ->assertViewHas('diterima', 1);
    }

    public function test_grafik_per_bulan_hanya_mengambil_tahun_sekarang(): void
    {
        $admin = User::factory()->admin()->create();

        $bulanIni = Report::factory()->create([
            'created_at' => now(),
        ]);

        TabelStatus::factory()
            ->for($bulanIni, 'laporan')
            ->create(['status' => 'diterima']);

        // Laporan tahun lalu tidak boleh ikut dihitung.
        $tahunLalu = Report::factory()->create([
            'created_at' => now()->subYear(),
        ]);

        TabelStatus::factory()
            ->for($tahunLalu, 'laporan')
            ->create(['status' => 'diterima']);

        $response = $this->actingAs($admin)
            ->get(route('admin.statistik.index'));

        $response->assertOk();

        $labelBulan = $response->viewData('labelBulan');
        $dataBulan = $response->viewData('dataBulan');

        $this->assertCount(12, $labelBulan);
        $this->assertCount(12, $dataBulan);
        $this->assertSame(1, $dataBulan[(int) now()->month - 1]);
    }

    public function test_nilai_statistik_konsisten_dengan_query_langsung(): void
    {
        $admin = User::factory()->admin()->create();

        $laporanA = $this->laporanDenganStatus('diterima');
        TabelStatus::factory()
            ->for($laporanA, 'laporan')
            ->create(['status' => 'selesai']);

        $this->laporanDenganStatus('ditolak');
        $this->laporanDenganStatus('diproses');

        // Hitung ulang langsung dari database sebagai pembanding.
        $latestIds = TabelStatus::query()
            ->selectRaw('MAX(id_status) as id_status')
            ->groupBy('report_id');

        $hitungLangsung = fn (string $status) => TabelStatus::query()
            ->joinSub($latestIds, 'latest', 'tabel_status.id_status', '=', 'latest.id_status')
            ->where('tabel_status.status', $status)
            ->count();

        $this->actingAs($admin)
            ->get(route('admin.statistik.index'))
            ->assertViewHas('total', Report::count())
            ->assertViewHas('diterima', $hitungLangsung('diterima'))
            ->assertViewHas('proses', $hitungLangsung('diproses'))
            ->assertViewHas('selesai', $hitungLangsung('selesai'))
            ->assertViewHas('ditolak', $hitungLangsung('ditolak'));
    }
}
