<?php

namespace Database\Factories;

use App\Models\JadwalPenjemuran;
use App\Models\Sampah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalPenjemuran>
 */
class JadwalPenjemuranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-1 month', '+1 week');

        return [
            'sampah_id' => Sampah::factory()->state(['jenis' => Sampah::JENIS_ORGANIK]),
            'tanggal_mulai' => $mulai->format('Y-m-d'),
            'tanggal_selesai' => (clone $mulai)->modify('+'.fake()->numberBetween(1, 7).' days')->format('Y-m-d'),
            'catatan' => fake()->optional(0.3)->sentence(),
        ];
    }
}
