<?php

namespace Tests\Feature;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Models\User;
use App\Services\LaporanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_laporan_or_unduh(): void
    {
        $this->get(route('laporan.index'))
            ->assertRedirect(route('login'));

        $this->get(route('laporan.unduh'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_and_pengelola_can_access_laporan_with_default_period(): void
    {
        $admin = User::factory()->admin()->create();
        $pengelola = User::factory()->create();

        $sampahBulanIni = Sampah::factory()->create([
            'user_id' => $pengelola->id,
            'tanggal_masuk' => now()->toDateString(),
            'jenis' => 'Organik',
            'berat_kg' => 14.50,
            'status' => Sampah::STATUS_DIKELOLA,
        ]);

        JadwalPenjemuran::factory()->create([
            'sampah_id' => $sampahBulanIni->id,
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
        ]);

        $this->actingAs($admin)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSeeText('Laporan Pengelolaan Sampah')
            ->assertSeeText('Organik')
            ->assertSeeText('14,50 kg')
            ->assertSeeText('Jadwal Penjemuran pada Periode Ini');

        $this->actingAs($pengelola)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSeeText('14,50 kg');
    }

    public function test_filter_periode_displays_matching_sampah_summary_and_intersecting_jadwal(): void
    {
        $user = User::factory()->create(['name' => 'Budi Pengelola']);

        // Data dalam periode filter (2026-09-01 s/d 2026-09-15)
        $sampahOrganik = Sampah::factory()->create([
            'user_id' => $user->id,
            'tanggal_masuk' => '2026-09-05',
            'jenis' => 'Organik',
            'berat_kg' => 10.25,
            'status' => Sampah::STATUS_MASUK,
        ]);
        $sampahDaun = Sampah::factory()->create([
            'user_id' => $user->id,
            'tanggal_masuk' => '2026-09-10',
            'jenis' => 'Daun Kering',
            'berat_kg' => 5.75,
            'status' => Sampah::STATUS_DIKELOLA,
        ]);

        // Data di luar periode filter
        Sampah::factory()->create([
            'user_id' => $user->id,
            'tanggal_masuk' => '2026-08-10',
            'jenis' => 'Plastik',
            'berat_kg' => 99.00,
        ]);

        // Jadwal beririsan dengan periode (mulai 2026-08-30 s/d 2026-09-03)
        JadwalPenjemuran::factory()->create([
            'sampah_id' => $sampahDaun->id,
            'tanggal_mulai' => '2026-08-30',
            'tanggal_selesai' => '2026-09-03',
        ]);

        $response = $this->actingAs($user)->get(route('laporan.index', [
            'tanggal_awal' => '2026-09-01',
            'tanggal_akhir' => '2026-09-15',
        ]));

        $response->assertOk();
        $response->assertSeeText('Organik');
        $response->assertSeeText('Daun Kering');
        $response->assertSeeText('16,00 kg'); // Total berat: 10.25 + 5.75
        $response->assertSeeText('10,25');
        $response->assertSeeText('5,75');
        $response->assertSeeText('Budi Pengelola');
        $response->assertDontSeeText('99,00 kg');
    }

    public function test_validation_error_when_tanggal_akhir_before_tanggal_awal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('laporan.index'))
            ->get(route('laporan.index', [
                'tanggal_awal' => '2026-10-15',
                'tanggal_akhir' => '2026-10-01',
            ]));

        $response->assertRedirect(route('laporan.index'));
        $response->assertSessionHasErrors([
            'tanggal_akhir' => 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
        ]);
    }

    public function test_empty_state_shown_and_download_button_disabled_when_no_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('laporan.index', [
            'tanggal_awal' => '2025-01-01',
            'tanggal_akhir' => '2025-01-31',
        ]));

        $response->assertOk();
        $response->assertSeeText('Tidak ada data laporan pada periode ini');
        $response->assertSee('aria-disabled="true"', false);
    }

    public function test_unduh_pdf_returns_a4_pdf_file_with_expected_filename(): void
    {
        $user = User::factory()->create(['name' => 'Siti Admin']);

        Sampah::factory()->create([
            'user_id' => $user->id,
            'tanggal_masuk' => '2026-10-05',
            'jenis' => 'Organik',
            'berat_kg' => 20.00,
        ]);

        $response = $this->actingAs($user)->get(route('laporan.unduh', [
            'tanggal_awal' => '2026-10-01',
            'tanggal_akhir' => '2026-10-19',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'laporan-sampah-2026-10-01-2026-10-19.pdf',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_laporan_service_returns_structured_data(): void
    {
        $user = User::factory()->create();
        Sampah::factory()->create([
            'user_id' => $user->id,
            'tanggal_masuk' => '2026-10-10',
            'jenis' => 'Organik',
            'berat_kg' => 8.5,
        ]);

        $service = app(LaporanService::class);
        $result = $service->ambilDataLaporan('2026-10-01', '2026-10-15');

        $this->assertSame('2026-10-01', $result['tanggalAwal']);
        $this->assertSame('2026-10-15', $result['tanggalAkhir']);
        $this->assertSame(8.5, $result['totalBerat']);
        $this->assertSame(8.5, $result['rekapPerJenis']->get('Organik'));
        $this->assertFalse($result['isEmpty']);
    }
}
