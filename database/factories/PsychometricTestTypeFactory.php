<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PsychometricTestType>
 */
class PsychometricTestTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Cognitive Ability',
                'Personality Assessment',
                'Emotional Intelligence',
                'Skills Aptitude',
                'Leadership Potential',
                'Behavioral Competency',
                'Numerical Reasoning',
                'Verbal Reasoning',
                'Logical Reasoning',
                'Situational Judgment'
            ]),
            'description' => fake()->sentence(),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
            'is_active' => fake()->boolean(90) // 90% chance of being active
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
