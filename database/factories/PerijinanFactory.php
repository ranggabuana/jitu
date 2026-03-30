<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perijinan>
 */
class PerijinanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_perijinan' => fake()->sentence(3) . ' Permit',
            'deskripsi' => fake()->paragraph(),
            'kode_perijinan' => strtoupper(fake()->unique()->bothify('???-####')),
            'lama_hari' => fake()->numberBetween(1, 30),
            'biaya' => fake()->randomFloat(2, 50000, 10000000),
            'status' => fake()->randomElement(['aktif', 'tidak_aktif']),
        ];
    }
}
