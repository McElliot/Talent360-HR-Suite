<?php

namespace Database\Seeders;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PsychometricTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 active tests with random types
        PsychometricTest::factory()
            ->count(10)
            ->create();

        // Create 3 inactive tests
        PsychometricTest::factory()
            ->count(3)
            ->inactive()
            ->create();

        // Create 5 tests specifically for Cognitive Ability
        $cognitiveType = PsychometricTestType::where('name', 'Cognitive Ability')->first();
        if ($cognitiveType) {
            PsychometricTest::factory()
                ->count(5)
                ->forType($cognitiveType->id)
                ->create();
        }
    }
}
