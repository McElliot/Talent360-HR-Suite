<?php

namespace Database\Seeders;

use App\Models\PsychometricTestType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PsychometricTestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 active test types
        PsychometricTestType::factory()
            ->count(5)
            ->create();

        // Create 2 inactive test types
        PsychometricTestType::factory()
            ->count(2)
            ->inactive()
            ->create();
    }
}
