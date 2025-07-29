<?php

namespace Database\Factories;

use App\Models\PsychometricTestType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PsychometricTest>
 */
class PsychometricTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'psychometric_test_type_id' => PsychometricTestType::inRandomOrder()->first()?->id,
            'title' => fake()->unique()->words(3, true) . ' Test',
            'instructions' => fake()->paragraph(),
            'duration_minutes' => fake()->numberBetween(15, 120),
            'is_active' => fake()->boolean(85),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
        ];
    }

    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
                // Optional: You could also set a deactivation reason
            ];
        });
    }

    public function forType(int $typeId): static
    {
        return $this->state(fn(array $attributes) => [
            'psychometric_test_type_id' => $typeId,
        ]);
    }

    /**
     * Create tests with short durations (5-30 minutes)
     */
    public function shortDuration(): static
    {
        return $this->state(fn(array $attributes) => [
            'duration_minutes' => fake()->numberBetween(5, 30),
        ]);
    }
}
