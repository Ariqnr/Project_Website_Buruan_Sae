<?php

namespace Tests\Feature;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard_with_summary_cards_and_chart(): void
    {
        $user = User::factory()->create(['name' => 'Budi Pengelola']);

        // Data sampah bulan ini
        Sampah::factory()->create([
            'tanggal_masuk' => now()->toDateString(),
            'berat_kg' => 25.50,
            'status' => Sampah::STATUS_DIKELOLA,
        ]);
        Sampah::factory()->create([
            'tanggal_masuk' => now()->toDateString(),
            'berat_kg' => 14.50,
            'status' => Sampah::STATUS_MASUK,
        ]);

        // Data sampah 2 bulan lalu
        Sampah::factory()->create([
            'tanggal_masuk' => now()->subMonths(2)->toDateString(),
            'berat_kg' => 50.00,
            'status' => Sampah::STATUS_SELESAI,
        ]);

        // Jadwal aktif (sampah dibuat di bulan lalu agar tidak mengubah total bulan ini)
        $sampahAktif = Sampah::factory()->create([
            'jenis' => 'Daun Kering Kompos',
            'tanggal_masuk' => now()->subMonth()->toDateString(),
            'berat_kg' => 10.00,
        ]);
        JadwalPenjemuran::factory()->for($sampahAktif)->create([
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'catatan' => 'Jemur di bedeng A',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Dashboard Operasional');
        $response->assertSeeText('Halo, Budi Pengelola!');

        // Kartu Ringkasan
        $response->assertSeeText('40,00'); // 25.50 + 14.50 = 40.00 kg bulan ini
        $response->assertSeeText('100,00'); // 40.00 + 50.00 + 10.00 = 100.00 kg total seluruh waktu
        $response->assertSeeText('Total Seluruh Waktu:');
        $response->assertSeeText('Sampah Sedang Dikelola');
        $response->assertSeeText('Jadwal Penjemuran Aktif');

        // 5 Catatan Terbaru
        $response->assertSeeText('5 Catatan Sampah Terbaru');
        $response->assertSeeText('Daun Kering Kompos');

        // Jadwal Penjemuran Aktif
        $response->assertSeeText('Jadwal Penjemuran Aktif');
        $response->assertSeeText('Jemur di bedeng A');

        // Grafik Larapex
        $response->assertSee('apexcharts', false);

        // Tombol Mode Gelap / Terang tersedia
        $response->assertSeeText('Mode Gelap');
        $response->assertSeeText('Mode Terang');
        $response->assertSee('toggleBuruanSaeTheme', false);
    }
}
