<?php

namespace Tests\Feature;

use App\Models\Sampah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SampahCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_sampah_index(): void
    {
        $this->get(route('sampah.index'))
            ->assertRedirect(route('login'));
    }

    public function test_can_render_sampah_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('sampah.create'))
            ->assertOk()
            ->assertSeeText('Tambah Data Sampah Baru')
            ->assertSee('name="berat_kg"', false);
    }

    public function test_can_render_sampah_edit_form(): void
    {
        $user = User::factory()->create();
        $item = Sampah::factory()->create(['berat_kg' => 25.50]);

        $this->actingAs($user)
            ->get(route('sampah.edit', $item))
            ->assertOk()
            ->assertSeeText('Ubah Data Sampah')
            ->assertSee('value="25.50"', false);
    }

    public function test_pengelola_can_view_sampah_index_with_pagination(): void
    {
        $pengelola = User::factory()->create();
        Sampah::factory()->count(15)->create();

        $response = $this->actingAs($pengelola)->get(route('sampah.index'));

        $response->assertOk();
        $response->assertSeeText('Data Pengelolaan Sampah');
        $response->assertSeeText('Tambah Data Sampah');

        // Memastikan pagination 10 per halaman
        $this->assertCount(10, $response->viewData('sampah'));
    }

    public function test_filter_by_status_and_jenis(): void
    {
        $user = User::factory()->create();
        Sampah::factory()->create([
            'jenis' => 'Daun Kering',
            'status' => Sampah::STATUS_MASUK,
        ]);
        Sampah::factory()->create([
            'jenis' => 'Plastik',
            'status' => Sampah::STATUS_DIKELOLA,
        ]);

        $response = $this->actingAs($user)->get(route('sampah.index', [
            'status' => Sampah::STATUS_MASUK,
            'jenis' => 'Daun',
        ]));

        $response->assertOk();
        $response->assertSeeText('Daun Kering');
        $response->assertDontSeeText('Plastik');
    }

    public function test_empty_state_displayed_when_no_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('sampah.index'));

        $response->assertOk();
        $response->assertSeeText('Belum ada data sampah. Klik Tambah Data untuk mulai mencatat.');
    }

    public function test_pengelola_cannot_see_hapus_button(): void
    {
        $pengelola = User::factory()->create();
        $item = Sampah::factory()->create();

        $response = $this->actingAs($pengelola)->get(route('sampah.index'));

        $response->assertOk();
        $response->assertSeeText('Ubah');
        $response->assertDontSeeText('Hapus');
    }

    public function test_admin_can_see_hapus_button(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Sampah::factory()->create();

        $response = $this->actingAs($admin)->get(route('sampah.index'));

        $response->assertOk();
        $response->assertSeeText('Ubah');
        $response->assertSeeText('Hapus');
    }

    public function test_pengelola_can_create_sampah_and_user_id_is_auto_filled(): void
    {
        $pengelola = User::factory()->create();

        $response = $this->actingAs($pengelola)->post(route('sampah.store'), [
            'tanggal_masuk' => now()->toDateString(),
            'jenis' => 'Organik',
            'berat_kg' => 15.75,
            'status' => Sampah::STATUS_MASUK,
            'catatan' => 'Sampah sayur dari pasar',
        ]);

        $response->assertRedirect(route('sampah.index'));
        $response->assertSessionHas('success', 'Data sampah berhasil disimpan.');

        $this->assertDatabaseHas('sampah', [
            'user_id' => $pengelola->id,
            'jenis' => 'Organik',
            'berat_kg' => 15.75,
            'status' => Sampah::STATUS_MASUK,
            'catatan' => 'Sampah sayur dari pasar',
        ]);
    }

    public function test_pengelola_can_update_sampah(): void
    {
        $pengelola = User::factory()->create();
        $item = Sampah::factory()->create([
            'status' => Sampah::STATUS_MASUK,
            'berat_kg' => 10.00,
        ]);

        $response = $this->actingAs($pengelola)->put(route('sampah.update', $item), [
            'tanggal_masuk' => $item->tanggal_masuk->toDateString(),
            'jenis' => $item->jenis,
            'berat_kg' => 12.50,
            'status' => Sampah::STATUS_DIKELOLA,
            'catatan' => 'Mulai diproses kompos',
        ]);

        $response->assertRedirect(route('sampah.index'));
        $response->assertSessionHas('success', 'Data sampah berhasil diperbarui.');

        $this->assertDatabaseHas('sampah', [
            'id' => $item->id,
            'berat_kg' => 12.50,
            'status' => Sampah::STATUS_DIKELOLA,
            'catatan' => 'Mulai diproses kompos',
        ]);
    }

    public function test_pengelola_cannot_delete_sampah_gets_forbidden(): void
    {
        $pengelola = User::factory()->create();
        $item = Sampah::factory()->create();

        $response = $this->actingAs($pengelola)->delete(route('sampah.destroy', $item));

        $response->assertForbidden();
        $this->assertDatabaseHas('sampah', ['id' => $item->id]);
    }

    public function test_admin_can_delete_sampah(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Sampah::factory()->create();

        $response = $this->actingAs($admin)->delete(route('sampah.destroy', $item));

        $response->assertRedirect(route('sampah.index'));
        $response->assertSessionHas('success', 'Data sampah berhasil dihapus.');

        $this->assertDatabaseMissing('sampah', ['id' => $item->id]);
    }

    public function test_validation_errors_are_in_indonesian(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sampah.store'), [
            'tanggal_masuk' => now()->addDays(2)->toDateString(), // masa depan
            'jenis' => 'JenisNgawur',
            'berat_kg' => -5,
            'status' => 'statusNgawur',
        ]);

        $response->assertSessionHasErrors([
            'tanggal_masuk' => 'Tanggal masuk tidak boleh di masa depan (maksimal hari ini).',
            'jenis' => 'Pilihan jenis sampah tidak valid.',
            'berat_kg' => 'Berat sampah harus lebih besar dari 0 kg.',
            'status' => 'Pilihan status tidak valid.',
        ]);
    }
}
