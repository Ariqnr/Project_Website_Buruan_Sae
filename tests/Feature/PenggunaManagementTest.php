<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PenggunaManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_pengelola_cannot_access_pengguna_management(): void
    {
        $this->get(route('pengguna.index'))
            ->assertRedirect(route('login'));

        $pengelola = User::factory()->create();

        $this->actingAs($pengelola)
            ->get(route('pengguna.index'))
            ->assertForbidden();
    }

    public function test_menu_pengguna_only_visible_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $pengelola = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('pengguna.index'), false);

        $this->actingAs($pengelola)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('pengguna.index'), false);
    }

    public function test_admin_can_view_pengguna_index_with_pagination(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(12)->create();

        $response = $this->actingAs($admin)->get(route('pengguna.index'));

        $response->assertOk();
        $response->assertSeeText('Manajemen Pengguna');
        $response->assertSeeText('Tambah Pengguna');
        $this->assertCount(10, $response->viewData('pengguna'));
    }

    public function test_admin_can_create_user_with_auto_generated_temp_password_and_must_change_password(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('pengguna.store'), [
            'name' => 'Rina Pengelola',
            'email' => 'rina@buruansae.test',
            'role' => User::ROLE_PENGELOLA,
        ]);

        $response->assertRedirect(route('pengguna.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('temp_password');

        $tempPassword = session('temp_password');
        $this->assertIsString($tempPassword);
        $this->assertGreaterThanOrEqual(10, strlen($tempPassword));

        $newUser = User::where('email', 'rina@buruansae.test')->firstOrFail();
        $this->assertSame('Rina Pengelola', $newUser->name);
        $this->assertSame(User::ROLE_PENGELOLA, $newUser->role);
        $this->assertTrue($newUser->is_active);
        $this->assertTrue($newUser->must_change_password);
        $this->assertTrue(Hash::check($tempPassword, $newUser->password));
    }

    public function test_admin_cannot_change_own_role_or_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        // Tidak boleh mengubah role sendiri
        $this->actingAs($admin)
            ->from(route('pengguna.edit', $admin))
            ->put(route('pengguna.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_PENGELOLA,
            ])
            ->assertRedirect(route('pengguna.edit', $admin))
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());

        // Tidak boleh menonaktifkan akun sendiri
        $this->actingAs($admin)
            ->patch(route('pengguna.toggle-status', $admin))
            ->assertRedirect(route('pengguna.index'))
            ->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_must_always_have_at_least_one_active_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $inactiveAdmin = User::factory()->admin()->inactive()->create();

        // Hanya ada 1 admin aktif ($admin), jadi tidak boleh ada penurunan role admin aktif terakhir
        $pengelola = User::factory()->create();

        // Buat admin kedua, lalu admin pertama menonaktifkan admin kedua -> boleh karena masih ada admin pertama
        $adminKedua = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('pengguna.toggle-status', $adminKedua))
            ->assertRedirect(route('pengguna.index'))
            ->assertSessionHas('success');

        $this->assertFalse($adminKedua->fresh()->is_active);
    }

    public function test_admin_can_toggle_status_and_reset_password(): void
    {
        $admin = User::factory()->admin()->create();
        $pengelola = User::factory()->create(['is_active' => true, 'must_change_password' => false]);

        // Nonaktifkan pengelola
        $this->actingAs($admin)
            ->patch(route('pengguna.toggle-status', $pengelola))
            ->assertRedirect(route('pengguna.index'));

        $this->assertFalse($pengelola->fresh()->is_active);

        // Aktifkan kembali pengelola
        $this->actingAs($admin)
            ->patch(route('pengguna.toggle-status', $pengelola))
            ->assertRedirect(route('pengguna.index'));

        $this->assertTrue($pengelola->fresh()->is_active);

        // Reset kata sandi pengelola
        $response = $this->actingAs($admin)
            ->post(route('pengguna.reset-password', $pengelola));

        $response->assertRedirect(route('pengguna.index'));
        $response->assertSessionHas('temp_password');

        $newTempPassword = session('temp_password');
        $this->assertGreaterThanOrEqual(10, strlen((string) $newTempPassword));
        $this->assertTrue($pengelola->fresh()->must_change_password);
        $this->assertTrue(Hash::check((string) $newTempPassword, $pengelola->fresh()->password));
    }

    public function test_inactive_user_cannot_login_and_sees_indonesian_message(): void
    {
        $inactiveUser = User::factory()->inactive()->create([
            'email' => 'nonaktif@buruansae.test',
            'password' => 'password',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'nonaktif@buruansae.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.',
        ]);
        $this->assertGuest();
    }

    public function test_deactivated_logged_in_user_is_automatically_logged_out_on_next_request(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        // Admin menonaktifkan akun saat user sedang login
        $user->update(['is_active' => false]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.',
        ]);
        $this->assertGuest();
    }

    public function test_user_with_must_change_password_is_redirected_to_profile_until_password_updated(): void
    {
        $user = User::factory()->mustChangePassword()->create([
            'password' => 'SandiSementara123',
        ]);

        // Mengakses dashboard atau halaman lain akan dialihkan ke halaman profil
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('warning', 'Demi keamanan, silakan ganti kata sandi sementara Anda terlebih dahulu.');

        $this->actingAs($user)
            ->get(route('sampah.index'))
            ->assertRedirect(route('profile.edit'));

        // Kata sandi baru tidak boleh sama dengan kata sandi lama
        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password' => 'SandiSementara123',
                'password' => 'SandiSementara123',
                'password_confirmation' => 'SandiSementara123',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrorsIn('updatePassword', [
                'password' => 'Kata sandi baru tidak boleh sama dengan kata sandi lama.',
            ]);

        // Kata sandi baru minimal 8 karakter
        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password' => 'SandiSementara123',
                'password' => 'pendek',
                'password_confirmation' => 'pendek',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrorsIn('updatePassword', [
                'password' => 'Kata sandi baru minimal harus 8 karakter.',
            ]);

        // Berhasil mengganti kata sandi baru yang valid
        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password' => 'SandiSementara123',
                'password' => 'SandiBaruKuat123',
                'password_confirmation' => 'SandiBaruKuat123',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'password-updated');

        $this->assertFalse($user->fresh()->must_change_password);
        $this->assertTrue(Hash::check('SandiBaruKuat123', $user->fresh()->password));

        // Sekarang dapat mengakses dashboard
        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertOk();
    }
}
