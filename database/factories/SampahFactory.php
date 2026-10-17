<?php

namespace Database\Factories;

use App\Models\Sampah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sampah>
 */
class SampahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tanggal_masuk' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'jenis' => fake()->randomElement(Sampah::JENIS),
            'berat_kg' => fake()->randomFloat(2, 0.5, 50),
            'status' => fake()->randomElement(array_keys(Sampah::STATUSES)),
            'catatan' => fake()->optional(0.4)->sentence(),
        ];
    }

    public function dikelola(): static
    {
        return $this->state(fn (array $attributes) => ['status' => Sampah::STATUS_DIKELOLA]);
    }
}
