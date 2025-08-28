<?php

namespace App\Repository;

use App\Models\PsychometricCompetence;

class PsychometricCompetenceRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createCompetence(array $data)
    {
        $competence = PsychometricCompetence::create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'weight' => $data['weight'] ?? 1.0,
            'is_active' => $data['is_active'] ?? true,
            'test_type_id' => $data['test_type_id'] ?? null,
        ]);

        // Attach to test if test_id is provided
        if (isset($data['test_id'])) {
            $competence->tests()->attach($data['test_id'], ['weight' => $data['weight'] ?? 1.0]);
        }

        return $competence;
    }
}
