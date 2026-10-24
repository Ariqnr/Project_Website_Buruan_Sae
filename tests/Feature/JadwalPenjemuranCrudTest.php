<?php

namespace Tests\Feature;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPenjemuranCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_jadwal_index(): void
    {
        $this->get(route('jadwal.index'))
            ->assertRedirect(route('login'));
    }

    public function test_can_render_jadwal_create_form_showing_only_organik_sampah(): void
    {
        $user = User::factory()->create();
        $sampahOrganik = Sampah::factory()->create([
            'jenis' => Sampah::JENIS_ORGANIK,
            'berat_kg' => 18.50,
        ]);
        $sampahNonOrganik = Sampah::factory()->create([
            'jenis' => 'Plastik',
            'berat_kg' => 9.25,
        ]);

        $this->actingAs($user)
            ->get(route('jadwal.create'))
            ->assertOk()
            ->assertSeeText('Tambah Jadwal Penjemuran Baru')
            ->assertSeeText('Organik')
            ->assertSeeText('18,50 kg')
            ->assertDontSeeText('9,25 kg');
    }

    public function test_can_render_jadwal_edit_form(): void
    {
        $user = User::factory()->create();
        $jadwal = JadwalPenjemuran::factory()->create([
            'catatan' => 'Jemur di rak timur',
        ]);

        $this->actingAs($user)
            ->get(route('jadwal.edit', $jadwal))
            ->assertOk()
            ->assertSeeText('Ubah Jadwal Penjemuran')
            ->assertSeeText('Jemur di rak timur');
    }

    public function test_pengelola_can_view_jadwal_index_with_pagination(): void
    {
        $pengelola = User::factory()->create();
        JadwalPenjemuran::factory()->count(15)->create();

        $response = $this->actingAs($pengelola)->get(route('jadwal.index'));

        $response->assertOk();
        $response->assertSeeText('Jadwal Penjemuran Sampah');
        $response->assertSeeText('Tambah Jadwal');

        $this->assertCount(10, $response->viewData('jadwal'));
    }

    public function test_status_jadwal_computed_automatically_and_filterable(): void
    {
        $user = User::factory()->create();

        $sampahAkanDatang = Sampah::factory()->create(['jenis' => Sampah::JENIS_ORGANIK]);
        $jadwalAkanDatang = JadwalPenjemuran::factory()->create([
            'sampah_id' => $sampahAkanDatang->id,
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'catatan' => 'Jadwal Akan Datang Unik',
        ]);

        $sampahBerjalan = Sampah::factory()->create(['jenis' => Sampah::JENIS_ORGANIK]);
        $jadwalBerjalan = JadwalPenjemuran::factory()->create([
            'sampah_id' => $sampahBerjalan->id,
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'catatan' => 'Jadwal Sedang Berjalan Unik',
        ]);

        $sampahSelesai = Sampah::factory()->create(['jenis' => Sampah::JENIS_ORGANIK]);
        $jadwalSelesai = JadwalPenjemuran::factory()->create([
            'sampah_id' => $sampahSelesai->id,
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDays(2)->toDateString(),
            'catatan' => 'Jadwal Selesai Unik',
        ]);

        $this->assertSame(JadwalPenjemuran::STATUS_AKAN_DATANG, $jadwalAkanDatang->status_jadwal);
        $this->assertSame(JadwalPenjemuran::STATUS_SEDANG_BERJALAN, $jadwalBerjalan->status_jadwal);
        $this->assertSame(JadwalPenjemuran::STATUS_SELESAI, $jadwalSelesai->status_jadwal);

        // Filter sedang berjalan
        $response = $this->actingAs($user)->get(route('jadwal.index', [
            'status' => JadwalPenjemuran::STATUS_SEDANG_BERJALAN,
        ]));

        $response->assertOk();
        $response->assertSeeText('Jadwal Sedang Berjalan Unik');
        $response->assertDontSeeText('Jadwal Akan Datang Unik');
        $response->assertDontSeeText('Jadwal Selesai Unik');
    }

    public function test_empty_state_displayed_when_no_jadwal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('jadwal.index'));

        $response->assertOk();
        $response->assertSeeText('Belum ada jadwal penjemuran. Klik Tambah Jadwal untuk membuat.');
    }

    public function test_creating_jadwal_automatically_updates_sampah_status_from_masuk_to_dikelola(): void
    {
        $pengelola = User::factory()->create();
        $sampah = Sampah::factory()->create([
            'jenis' => Sampah::JENIS_ORGANIK,
            'status' => Sampah::STATUS_MASUK,
        ]);

        $response = $this->actingAs($pengelola)->post(route('jadwal.store'), [
            'sampah_id' => $sampah->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'catatan' => 'Mulai dijemur pagi hari',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal penjemuran berhasil disimpan.');

        $this->assertDatabaseHas('jadwal_penjemuran', [
            'sampah_id' => $sampah->id,
            'catatan' => 'Mulai dijemur pagi hari',
        ]);

        $this->assertDatabaseHas('sampah', [
            'id' => $sampah->id,
            'status' => Sampah::STATUS_DIKELOLA,
        ]);
    }

    public function test_creating_jadwal_keeps_selesai_status_unchanged(): void
    {
        $pengelola = User::factory()->create();
        $sampah = Sampah::factory()->create([
            'jenis' => Sampah::JENIS_ORGANIK,
            'status' => Sampah::STATUS_SELESAI,
        ]);

        $this->actingAs($pengelola)->post(route('jadwal.store'), [
            'sampah_id' => $sampah->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
        ]);

        $this->assertDatabaseHas('sampah', [
            'id' => $sampah->id,
            'status' => Sampah::STATUS_SELESAI,
        ]);
    }

    public function test_non_organik_sampah_cannot_be_scheduled_for_penjemuran(): void
    {
        $pengelola = User::factory()->create();
        $sampahAnorganik = Sampah::factory()->create([
            'jenis' => 'Anorganik',
            'status' => Sampah::STATUS_MASUK,
        ]);

        $response = $this->actingAs($pengelola)
            ->from(route('jadwal.create'))
            ->post(route('jadwal.store'), [
                'sampah_id' => $sampahAnorganik->id,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addDays(2)->toDateString(),
            ]);

        $response->assertRedirect(route('jadwal.create'));
        $response->assertSessionHasErrors([
            'sampah_id' => 'Hanya sampah kategori Organik yang dapat dijadwalkan untuk penjemuran.',
        ]);

        $this->assertDatabaseMissing('jadwal_penjemuran', [
            'sampah_id' => $sampahAnorganik->id,
        ]);
    }

    public function test_pengelola_can_update_jadwal(): void
    {
        $pengelola = User::factory()->create();
        $jadwal = JadwalPenjemuran::factory()->create();

        $newStart = now()->addDay()->toDateString();
        $newEnd = now()->addDays(4)->toDateString();

        $response = $this->actingAs($pengelola)->put(route('jadwal.update', $jadwal), [
            'sampah_id' => $jadwal->sampah_id,
            'tanggal_mulai' => $newStart,
            'tanggal_selesai' => $newEnd,
            'catatan' => 'Diperpanjang karena mendung',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal penjemuran berhasil diperbarui.');

        $this->assertDatabaseHas('jadwal_penjemuran', [
            'id' => $jadwal->id,
            'catatan' => 'Diperpanjang karena mendung',
        ]);
    }

    public function test_pengelola_cannot_delete_jadwal_gets_forbidden(): void
    {
        $pengelola = User::factory()->create();
        $jadwal = JadwalPenjemuran::factory()->create();

        $response = $this->actingAs($pengelola)->delete(route('jadwal.destroy', $jadwal));

        $response->assertForbidden();
        $this->assertDatabaseHas('jadwal_penjemuran', ['id' => $jadwal->id]);
    }

    public function test_admin_can_delete_jadwal(): void
    {
        $admin = User::factory()->admin()->create();
        $jadwal = JadwalPenjemuran::factory()->create();

        $response = $this->actingAs($admin)->delete(route('jadwal.destroy', $jadwal));

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal penjemuran berhasil dihapus.');

        $this->assertDatabaseMissing('jadwal_penjemuran', ['id' => $jadwal->id]);
    }

    public function test_validation_errors_are_in_indonesian(): void
    {
        $user = User::factory()->create();
        $sampah = Sampah::factory()->create(['jenis' => Sampah::JENIS_ORGANIK]);

        $response = $this->actingAs($user)->post(route('jadwal.store'), [
            'sampah_id' => 99999,
            'tanggal_mulai' => '2026-10-20',
            'tanggal_selesai' => '2026-10-18', // lebih awal dari tanggal_mulai
        ]);

        $response->assertSessionHasErrors([
            'sampah_id' => 'Data sampah yang dipilih tidak ditemukan.',
            'tanggal_selesai' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);
    }
}
