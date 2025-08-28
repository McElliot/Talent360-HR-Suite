<?php

namespace App\Services;

use App\Models\PsychometricCompetence;
use App\Repository\PsychometricCompetenceRepository;

class PsychometricTestCompetenceService
{
    protected $psychometricTestCompetenceRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(PsychometricCompetenceRepository $psychometricTestCompetenceRepository)
    {
        $this->psychometricTestCompetenceRepository = $psychometricTestCompetenceRepository;
    }

    public function createCompetence(array $data)
    {
        return $this->psychometricTestCompetenceRepository->createCompetence($data);
    }

    public function updateCompetence($competenceId, array $data)
    {
        $competence = PsychometricCompetence::findOrFail($competenceId);

        $competence->update([
            'name'        => $data['name'] ?? $competence->name,
            'code'        => $data['code'] ?? $competence->code,
            'description' => $data['description'] ?? $competence->description,
            'test_type_id' => $data['test_type_id'] ?? $competence->test_type_id,
            'sort_order'  => $data['sort_order'] ?? $competence->sort_order,
        ]);

        if (isset($data['test_id'])) {
            $competence->tests()->updateExistingPivot(
                $data['test_id'],
                ['weight' => $data['weight'] ?? 1]
            );
        }
    }
}
