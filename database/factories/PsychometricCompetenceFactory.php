<?php

namespace Database\Factories;

use App\Models\PsychometricTestType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PsychometricCompetenceFactory extends Factory
{
    protected static $usedCodes = [];
    protected static $usedNames = [];

    public function definition(): array
    {
        $code = $this->generateUniqueCode();
        $name = $this->generateUniqueName();

        return [
            'name' => $name,
            'code' => $code,
            'description' => fake()->sentence(),
            'test_type_id' => PsychometricTestType::factory(),
            'sort_order' => fake()->numberBetween(1, 100)
        ];
    }

    protected function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(3));
        } while (in_array($code, self::$usedCodes));

        self::$usedCodes[] = $code;
        return $code;
    }

    protected function generateUniqueName(): string
    {
        do {
            $name = fake()->words(3, true);
        } while (in_array($name, self::$usedNames));

        self::$usedNames[] = $name;
        return $name;
    }

    public function forTestType($testTypeId)
    {
        return $this->state(function (array $attributes) use ($testTypeId) {
            return [
                'test_type_id' => $testTypeId
            ];
        });
    }
}
