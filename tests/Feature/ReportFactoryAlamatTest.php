<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Factory `Report` harus menghasilkan alamat yang berbeda-beda.
 *
 * Dua tempat bergantung pada itu:
 *
 *   1. `StatistikController` menghitung "Lokasi Terpadat" dengan
 *      `groupBy('alamat')` + `COUNT(*)`. Alamat kembar membuat dua
 *      laporan jadi satu baris dan jumlahnya keliru.
 *   2. `test_riwayat_laporan_hanya_menampilkan_laporan_sendiri`
 *      memisahkan laporan satu pelapor dari laporan pelapor lain
 *      dengan `assertDontSee`. Alamat kembar membuat teks yang
 *      seharusnya tidak muncul ikut muncul, jadi test gagal.
 *
 * Kolom `alamat` sengaja `text` tanpa unique index di database,
 * jadi jaminan ini hanya bisa datang dari factory.
 */
class ReportFactoryAlamatTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_tidak_pernah_mengulang_alamat(): void
    {
        $alamat = Report::factory()
            ->count(300)
            ->create()
            ->pluck('alamat');

        $jumlahUnik = $alamat->unique()->count();

        $this->assertCount(300, $alamat, 'Semua laporan harus tersimpan.');
        $this->assertSame(
            300,
            $jumlahUnik,
            'Factory menghasilkan alamat kembar. Kolom `alamat` di database '
            . 'adalah `text` tanpa unique index, jadi factory satu-satunya '
            . 'tempat jaminan ini bisa diberikan.'
        );
    }

    public function test_alamat_terbaca_sebagai_alamat_purwakarta(): void
    {
        $laporan = Report::factory()->create();

        $this->assertStringContainsString('Purwakarta', $laporan->alamat);
        $this->assertStringStartsWith('Jl. ', $laporan->alamat);
        $this->assertMatchesRegularExpression(
            '/^Jl\. [A-Z][a-z]+ No\. \d+, Baca, Purwakarta$/',
            $laporan->alamat
        );
    }

    public function test_lokasi_terpadat_menghitung_per_alamat(): void
    {
        $laporan = Report::factory()->count(20)->create();

        $terhitung = \App\Models\Report::query()
            ->select('alamat')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('alamat')
            ->get();

        $this->assertSame(
            $laporan->pluck('alamat')->unique()->count(),
            $terhitung->count(),
            'Jumlah baris "lokasi terpadat" harus sama dengan jumlah alamat unik.'
        );

        $this->assertSame(
            0,
            $terhitung->where('jumlah', '>', 1)->count(),
            'Tidak boleh ada alamat dengan jumlah lebih dari satu, kalau tidak '
            . 'perhitungan lokasi terpadat salah.'
        );
    }
}
