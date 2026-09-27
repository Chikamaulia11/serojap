<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat laporan beserta status terakhirnya.
     */
    private function buatLaporan(string $status, array $atribut = []): Report
    {
        $laporan = Report::factory()->create($atribut);

        TabelStatus::factory()
            ->for($laporan, 'laporan')
            ->create(['status' => $status]);

        return $laporan;
    }

    public function test_guest_bisa_mengakses_beranda(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_beranda_menampilkan_statistik_dari_database(): void
    {
        Report::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Total Laporan');
        $response->assertSee('3');
        $response->assertViewHas('totalLaporan', 3);
    }

    public function test_beranda_menghitung_laporan_per_status_terbaru(): void
    {
        // Empat laporan, masing-masing satu status awal.
        $baruDiterima = $this->buatLaporan('diterima');
        $baruDiproses = $this->buatLaporan('diproses');
        $this->buatLaporan('selesai');
        $this->buatLaporan('ditolak');

        // Dua di antaranya lalu diperbarui statusnya. Karena status terakhir
        // ditentukan oleh id_status terbesar, hanya status terbaru yang
        // ikut dihitung.
        TabelStatus::factory()
            ->for($baruDiterima, 'laporan')
            ->create(['status' => 'diproses']);

        TabelStatus::factory()
            ->for($baruDiproses, 'laporan')
            ->create(['status' => 'selesai']);

        $response = $this->get('/');

        $response->assertViewHas('totalLaporan', 4);
        $response->assertViewHas('jumlahPerStatus', [
            'diterima' => 0,
            'diproses' => 1,
            'selesai' => 2,
            'ditolak' => 1,
        ]);
    }

    public function test_beranda_menghitung_rata_rata_lama_penanganan(): void
    {
        $laporanA = Report::factory()->create([
            'created_at' => now()->subDays(10),
        ]);

        $laporanB = Report::factory()->create([
            'created_at' => now()->subDays(20),
        ]);

        TabelStatus::factory()
            ->for($laporanA, 'laporan')
            ->create(['status' => 'selesai', 'created_at' => now()->subDays(6)]);

        TabelStatus::factory()
            ->for($laporanB, 'laporan')
            ->create(['status' => 'selesai', 'created_at' => now()]);

        $response = $this->get('/');

        // Selisih 4 hari dan 20 hari, rata-ratanya 12 hari.
        $response->assertViewHas('rataRataHariPenanganan', 12.0);
    }

    public function test_rata_rata_lama_penanganan_null_bila_belum_ada_laporan_selesai(): void
    {
        $this->buatLaporan('diproses');

        $this->get('/')
            ->assertViewHas('rataRataHariPenanganan', null);
    }

    public function test_beranda_tidak_membocorkan_nama_pelapor(): void
    {
        $this->buatLaporan('diterima', [
            'nama_pelapor' => 'Siti Rahmawati Rahayu',
            'keterangan' => 'Jalan berlubang besar di depan sekolah.',
        ]);

        $this->buatLaporan('selesai', [
            'nama_pelapor' => 'Bagus Prasetyo Wibowo',
            'keterangan' => 'Lumpur lumpur tidak hilang setelah hujan.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Siti Rahmawati Rahayu');
        $response->assertDontSee('Bagus Prasetyo Wibowo');
    }

    public function test_beranda_tidak_membocorkan_nama_pelapor_dari_histori_lama(): void
    {
        $laporan = Report::factory()->create([
            'nama_pelapor' => 'NamaRahasiaPelapor',
        ]);

        TabelStatus::factory()
            ->for($laporan, 'laporan')
            ->create(['status' => 'diterima']);

        // Ubah nama pelapor menjadi "rahasia" pada baris yang sama,
        // halaman tetap tidak boleh menampilkannya.
        $laporan->update(['nama_pelapor' => 'NamaRahasiaPelapor']);

        $this->get('/')
            ->assertDontSee('NamaRahasiaPelapor');
    }

    public function test_beranda_tidak_menampilkan_foto_asli_pelapor(): void
    {
        $this->buatLaporan('diterima', [
            'foto' => 'reports/foto-rahasia-pelapor.jpg',
        ]);

        $this->get('/')
            ->assertDontSee('foto-rahasia-pelapor.jpg');
    }

    public function test_beranda_menampilkan_laporan_terbaru(): void
    {
        $laporanTerbaru = $this->buatLaporan('diproses', [
            'alamat' => 'Jl. Raya Indik Coba Alamat',
            'keterangan' => 'Lubang besar sekali di tengah jalan raya.',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($laporanTerbaru->alamat);
        $response->assertSee('Diproses');
    }

    public function test_keterangan_publik_dipotong_agar_pendek(): void
    {
        $keteranganPanjang = str_repeat('Jalan berlubang besar dan berbahaya. ', 20);

        $laporan = $this->buatLaporan('diterima', [
            'keterangan' => $keteranganPanjang,
        ]);

        $response = $this->get('/');

        $response->assertViewHas('laporanTerbaru', function ($laporanTerbaru) use ($laporan) {
            $terlihat = $laporanTerbaru->firstWhere('id', $laporan->id);

            return $terlihat !== null
                && $terlihat->keterangan_pendek !== $terlihat->keterangan
                && strlen($terlihat->keterangan_pendek) <= 123;
        });
    }

    public function test_beranda_membatasi_jumlah_laporan_publik(): void
    {
        Report::factory()->count(15)->create();

        $response = $this->get('/');

        $response->assertViewHas('laporanTerbaru', function ($laporanTerbaru) {
            return $laporanTerbaru->count() === 9;
        });
    }

    public function test_beranda_menampilkan_langkah_cara_kerja(): void
    {
        $response = $this->get('/');

        $response->assertSee('Ambil foto & titik lokasi', false);
        $response->assertSee('Isi keterangan');
        $response->assertSee('Diverifikasi petugas');
        $response->assertSee('Pantau status sampai selesai');
    }

    public function test_beranda_menampilkan_faq(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Apakah saya perlu akun untuk melapor jalan rusak?');
        $response->assertSee('Berapa lama laporan saya diproses?');
        $response->assertSee('Apakah data pribadi saya aman?');
        $response->assertSee('Bagaimana cara cek status laporan saya sendiri?');
        $response->assertSee('Jenis kerusakan apa saja yang bisa dilaporkan?');
    }

    public function test_beranda_tetap_terbuka_setelah_akun_dihapus(): void
    {
        $this->withSession(['account_deleted' => 'Akun berhasil dihapus.'])
            ->get('/')
            ->assertOk()
            ->assertSee('Akun Tidak Ditemukan');
    }

    public function test_beranda_menampilkan_dua_halaman_langsung(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $this->actingAs($pelapor)
            ->get('/')
            ->assertOk();
    }
}
