<?php

namespace Database\Seeders;

use App\Models\PsychometricCompetence;
use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use Illuminate\Database\Seeder;

class PsychometricCompetenceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing records if needed
        // PsychometricCompetence::truncate();

        $testTypes = [
            'Cognitive Ability' => [
                ['name' => 'Verbal Reasoning', 'code' => 'VRB'],
                ['name' => 'Numerical Reasoning', 'code' => 'NUM'],
                ['name' => 'Logical Reasoning', 'code' => 'LOG'],
                ['name' => 'Spatial Awareness', 'code' => 'SPA'],
                ['name' => 'Problem Solving', 'code' => 'PRB']
            ],
            'Personality Assessment' => [
                ['name' => 'Openness', 'code' => 'OPN'],
                ['name' => 'Conscientiousness', 'code' => 'CON'],
                ['name' => 'Extraversion', 'code' => 'EXT'],
                ['name' => 'Agreeableness', 'code' => 'AGR'],
                ['name' => 'Neuroticism', 'code' => 'NEU']
            ],
            'Leadership' => [
                ['name' => 'Strategic Thinking', 'code' => 'STR'],
                ['name' => 'Decision Making', 'code' => 'DEC'],
                ['name' => 'Team Leadership', 'code' => 'TLD'],
                ['name' => 'Communication', 'code' => 'COM'],
                ['name' => 'Emotional Intelligence', 'code' => 'EMO']
            ]
        ];

        $sortOrder = 10;

        foreach ($testTypes as $typeName => $competencies) {
            $testType = PsychometricTestType::firstOrCreate(
                ['name' => $typeName],
                ['description' => "Measures $typeName competencies"]
            );

            foreach ($competencies as $competence) {
                PsychometricCompetence::firstOrCreate(
                    ['code' => $competence['code']], // check uniqueness
                    [
                        'name' => $competence['name'],
                        'description' => "Measures {$competence['name']} ability",
                        'test_type_id' => $testType->id,
                        'sort_order' => $sortOrder
                    ]
                );

                $sortOrder += 10;
            }
        }

        // Create additional random competencies (5 of each type)
        PsychometricTestType::all()->each(function ($type) {
            PsychometricCompetence::factory()
                ->count(5)
                ->forTestType($type->id)
                ->create(['sort_order' => fn() => rand(10, 1000)]);
        });

        // Now assign competences to tests randomly
        $tests = PsychometricTest::all();
        $competences = PsychometricCompetence::all();

        foreach ($tests as $test) {
            // Get competences that match the test's type
            $eligibleCompetences = $competences->where('test_type_id', $test->psychometric_test_type_id);

            if ($eligibleCompetences->isNotEmpty()) {
                // Select random number of competences (1-5)
                $randomCompetences = $eligibleCompetences->random(rand(1, min(5, $eligibleCompetences->count())));

                // Attach with random weights between 0.5 and 3.0
                $attachData = $randomCompetences->mapWithKeys(function ($competence) {
                    return [$competence->id => ['weight' => rand(5, 30) / 10]]; // 0.5 to 3.0 in 0.1 increments
                });

                $test->competences()->attach($attachData);
            }
        }
    }
}
