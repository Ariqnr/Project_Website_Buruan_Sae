<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Sampah;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_timezone_is_asia_jakarta(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
    }

    public function test_guest_and_pengelola_cannot_access_log_page(): void
    {
        $this->get(route('log.index'))
            ->assertRedirect(route('login'));

        $pengelola = User::factory()->create();

        $this->actingAs($pengelola)
            ->get(route('log.index'))
            ->assertForbidden();
    }

    public function test_menu_log_aktivitas_only_visible_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $pengelola = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Log Aktivitas');

        $this->actingAs($pengelola)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSeeText('Log Aktivitas');
    }

    public function test_login_success_login_failed_and_logout_are_logged_without_passwords(): void
    {
        $user = User::factory()->create([
            'name' => 'Ariq Pengelola',
            'email' => 'ariq@buruansae.test',
            'password' => 'Rahasia123',
        ]);

        // 1. Login gagal
        $this->from('/login')->post('/login', [
            'email' => 'ariq@buruansae.test',
            'password' => 'SandiSalah999',
        ]);

        $failedLog = ActivityLog::where('aksi', ActivityLog::AKSI_LOGIN_GAGAL)->latest('id')->first();
        $this->assertNotNull($failedLog);
        $this->assertStringContainsString('ariq@buruansae.test', $failedLog->deskripsi);
        $this->assertStringNotContainsString('SandiSalah999', json_encode($failedLog->toArray()));

        // 2. Login berhasil
        $this->post('/login', [
            'email' => 'ariq@buruansae.test',
            'password' => 'Rahasia123',
        ]);

        $successLog = ActivityLog::where('aksi', ActivityLog::AKSI_LOGIN_BERHASIL)->latest('id')->first();
        $this->assertNotNull($successLog);
        $this->assertSame('Ariq Pengelola berhasil masuk ke sistem', $successLog->deskripsi);

        // 3. Logout
        $this->post('/logout');

        $logoutLog = ActivityLog::where('aksi', ActivityLog::AKSI_LOGOUT)->latest('id')->first();
        $this->assertNotNull($logoutLog);
        $this->assertSame('Ariq Pengelola keluar dari sistem', $logoutLog->deskripsi);
    }

    public function test_sampah_crud_logs_activity_with_data_lama_and_data_baru(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Ariq']);

        // Tambah sampah
        $this->actingAs($admin)->post(route('sampah.store'), [
            'tanggal_masuk' => now()->toDateString(),
            'jenis' => Sampah::JENIS_ORGANIK,
            'berat_kg' => 12.50,
            'status' => Sampah::STATUS_MASUK,
            'catatan' => 'Catatan awal',
        ]);

        $sampah = Sampah::latest('id')->firstOrFail();

        $logTambah = ActivityLog::where('aksi', ActivityLog::AKSI_TAMBAH)
            ->where('objek_tipe', 'Sampah')
            ->latest('id')
            ->first();

        $this->assertNotNull($logTambah);
        $this->assertSame('Ariq menambah data sampah Organik 12,50 kg', $logTambah->deskripsi);

        // Ubah sampah
        $this->actingAs($admin)->put(route('sampah.update', $sampah), [
            'tanggal_masuk' => now()->toDateString(),
            'jenis' => Sampah::JENIS_ORGANIK,
            'berat_kg' => 15.00,
            'status' => Sampah::STATUS_DIKELOLA,
            'catatan' => 'Catatan diubah',
        ]);

        $logUbah = ActivityLog::where('aksi', ActivityLog::AKSI_UBAH)
            ->where('objek_tipe', 'Sampah')
            ->latest('id')
            ->first();

        $this->assertNotNull($logUbah);
        $this->assertSame('12.50', (string) $logUbah->data_lama['berat_kg']);
        $this->assertSame('15.00', (string) $logUbah->data_baru['berat_kg']);
        $this->assertSame('masuk', $logUbah->data_lama['status']);
        $this->assertSame('dikelola', $logUbah->data_baru['status']);

        // Hapus sampah
        $this->actingAs($admin)->delete(route('sampah.destroy', $sampah));

        $logHapus = ActivityLog::where('aksi', ActivityLog::AKSI_HAPUS)
            ->where('objek_tipe', 'Sampah')
            ->latest('id')
            ->first();

        $this->assertNotNull($logHapus);
        $this->assertSame('Ariq menghapus data sampah Organik 15,00 kg', $logHapus->deskripsi);
    }

    public function test_jadwal_pengguna_password_and_pdf_actions_are_logged_and_sanitized(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $sampah = Sampah::factory()->create(['jenis' => Sampah::JENIS_ORGANIK]);

        // Tambah jadwal
        $this->actingAs($admin)->post(route('jadwal.store'), [
            'sampah_id' => $sampah->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'catatan' => 'Jemur pagi',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'aksi' => ActivityLog::AKSI_TAMBAH,
            'objek_tipe' => 'JadwalPenjemuran',
        ]);

        // Tambah pengguna baru
        $this->actingAs($admin)->post(route('pengguna.store'), [
            'name' => 'Petugas Baru',
            'email' => 'petugas@buruansae.test',
            'role' => User::ROLE_PENGELOLA,
        ]);

        $logUser = ActivityLog::where('objek_tipe', 'User')
            ->where('aksi', ActivityLog::AKSI_TAMBAH)
            ->latest('id')
            ->first();

        $this->assertNotNull($logUser);
        $this->assertArrayNotHasKey('password', $logUser->data_baru ?? []);
        $this->assertArrayNotHasKey('remember_token', $logUser->data_baru ?? []);

        // Unduh laporan PDF
        $this->actingAs($admin)->get(route('laporan.unduh', [
            'tanggal_awal' => '2026-10-01',
            'tanggal_akhir' => '2026-10-23',
        ]));

        $this->assertDatabaseHas('activity_logs', [
            'aksi' => ActivityLog::AKSI_UNDUH_PDF,
            'objek_tipe' => 'Laporan',
        ]);

        // Uji sanitasi langsung pada ActivityLogger
        $sanitizedLog = ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UBAH,
            deskripsi: 'Uji sanitasi field sensitif',
            dataLama: ['name' => 'Lama', 'password' => 'rahasia1', 'remember_token' => 'tok1'],
            dataBaru: ['name' => 'Baru', 'password' => 'rahasia2', 'token' => 'tok2'],
        );

        $this->assertNotNull($sanitizedLog);
        $this->assertArrayNotHasKey('password', $sanitizedLog->data_lama ?? []);
        $this->assertArrayNotHasKey('remember_token', $sanitizedLog->data_lama ?? []);
        $this->assertArrayNotHasKey('password', $sanitizedLog->data_baru ?? []);
        $this->assertArrayNotHasKey('token', $sanitizedLog->data_baru ?? []);
    }

    public function test_admin_can_view_and_filter_log_page_with_lihat_perubahan(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Filter']);

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UBAH,
            deskripsi: 'Admin Filter mengubah data sampah Organik 10,00 kg',
            dataLama: ['berat_kg' => '10.00'],
            dataBaru: ['berat_kg' => '12.00'],
            userId: $admin->id,
        );

        ActivityLogger::catat(
            aksi: ActivityLog::AKSI_UNDUH_PDF,
            deskripsi: 'Admin Filter mengunduh laporan PDF periode 2026-10-01 s/d 2026-10-23',
            userId: $admin->id,
        );

        $response = $this->actingAs($admin)->get(route('log.index', [
            'aksi' => ActivityLog::AKSI_UBAH,
            'user_id' => $admin->id,
        ]));

        $response->assertOk();
        $response->assertSeeText('Admin Filter mengubah data sampah Organik 10,00 kg');
        $response->assertSeeText('Lihat Perubahan');
        $response->assertDontSeeText('Admin Filter mengunduh laporan PDF');
    }
}
