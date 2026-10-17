<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered_in_indonesian(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSeeText('Email')
            ->assertSeeText('Kata Sandi')
            ->assertSeeText('Ingat saya')
            ->assertSeeText('Masuk');
    }

    public function test_admin_can_login_and_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_pengelola_can_login_and_is_redirected_to_dashboard(): void
    {
        $pengelola = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $pengelola->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($pengelola);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_wrong_password_shows_indonesian_error(): void
    {
        $user = User::factory()->create();

        $this->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'salah-sandi',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah. Silakan periksa kembali.']);

        $this->assertGuest();
    }

    public function test_empty_fields_show_indonesian_validation_errors(): void
    {
        $this->from('/login')
            ->post('/login', ['email' => '', 'password' => ''])
            ->assertSessionHasErrors([
                'email' => 'Email wajib diisi.',
                'password' => 'Kata sandi wajib diisi.',
            ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Orang Baru',
            'email' => 'baru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'baru@example.com']);
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
