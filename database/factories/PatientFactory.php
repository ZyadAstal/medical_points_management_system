<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'full_name' => fake()->name(),
            'national_id' => fake()->unique()->numerify('##########'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'points' => 0,
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
        ];
    }
}
