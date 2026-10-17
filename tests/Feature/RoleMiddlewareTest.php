<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])
            ->get('/_test/khusus-admin', fn () => 'ok-admin');

        Route::middleware(['web', 'auth', 'role:admin,pengelola'])
            ->get('/_test/semua-role', fn () => 'ok-semua');
    }

    public function test_admin_can_access_admin_only_route(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/_test/khusus-admin')
            ->assertOk()
            ->assertSee('ok-admin');
    }

    public function test_pengelola_gets_friendly_403_on_admin_only_route(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/_test/khusus-admin')
            ->assertForbidden()
            ->assertSeeText('Akses Ditolak')
            ->assertSeeText('Maaf, Anda tidak memiliki izin untuk membuka halaman ini.');
    }

    public function test_multiple_roles_are_allowed(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/_test/semua-role')
            ->assertOk();

        $this->actingAs(User::factory()->create())
            ->get('/_test/semua-role')
            ->assertOk();
    }

    public function test_unknown_role_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'tamu']))
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_dashboard_greets_user_with_name_and_role(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Budi']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Halo, Budi!')
            ->assertSeeText('Admin');
    }

    public function test_navigation_menu_is_shown(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertSeeText('Data Sampah')
            ->assertSeeText('Jadwal Penjemuran')
            ->assertSeeText('Laporan')
            ->assertSeeText('Keluar');
    }

    public function test_placeholder_pages_are_reachable(): void
    {
        $user = User::factory()->create();

        foreach (['sampah.index', 'jadwal.index', 'laporan.index'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }
}
