<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Buat akun awal. Tidak ada registrasi mandiri, akun hanya dibuat dari sini.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@buruansae.test'],
            [
                'name' => 'Admin Buruan Sae',
                'password' => 'password', // otomatis di-hash oleh cast 'hashed'
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'pengelola@buruansae.test'],
            [
                'name' => 'Pengelola Buruan Sae',
                'password' => 'password',
                'role' => User::ROLE_PENGELOLA,
                'email_verified_at' => now(),
            ],
        );
    }
}
