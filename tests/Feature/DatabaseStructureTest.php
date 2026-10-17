<?php

namespace Tests\Feature;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_default_role_is_pengelola(): void
    {
        $user = User::factory()->create();

        $this->assertSame(User::ROLE_PENGELOLA, $user->role);
        $this->assertTrue($user->isPengelola());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_has_many_sampah_and_sampah_has_many_jadwal(): void
    {
        $user = User::factory()->create();
        $sampah = Sampah::factory()->for($user)->create();
        JadwalPenjemuran::factory()->count(2)->for($sampah)->create();

        $this->assertCount(1, $user->sampah);
        $this->assertTrue($sampah->user->is($user));
        $this->assertCount(2, $sampah->jadwalPenjemuran);
    }

    public function test_deleting_sampah_cascades_to_jadwal(): void
    {
        $jadwal = JadwalPenjemuran::factory()->create();

        $jadwal->sampah->delete();

        $this->assertModelMissing($jadwal);
    }

    public function test_aktif_scope_only_returns_running_jadwal(): void
    {
        $sampah = Sampah::factory()->create();

        $aktif = JadwalPenjemuran::factory()->for($sampah)->create([
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
        ]);
        JadwalPenjemuran::factory()->for($sampah)->create([
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->subDays(5)->toDateString(),
        ]);

        $this->assertSame([$aktif->id], JadwalPenjemuran::aktif()->pluck('id')->all());
    }

    public function test_seeder_creates_admin_pengelola_and_sample_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@buruansae.test')->firstOrFail();
        $pengelola = User::where('email', 'pengelola@buruansae.test')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($pengelola->isPengelola());
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertNotSame('password', $admin->password);

        $this->assertSame(15, Sampah::count());
        $this->assertSame(4, JadwalPenjemuran::count());
        $this->assertSame(2, JadwalPenjemuran::aktif()->count());
    }
}
