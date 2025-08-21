<?php

namespace App\Services;

use App\Models\PsychometricTest;
use App\Repository\PsychometricTestRepository;
use Illuminate\Support\Facades\Auth;

class PsychometricTestService
{
    protected $psychometricTestRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(PsychometricTestRepository $psychometricTestRepository)
    {
        $this->psychometricTestRepository = $psychometricTestRepository;
    }
    /**
     * Create a new psychometric test.
     *
     * @param array $data
     * @return \App\Models\PsychometricTest
     */
    public function createTest(array $data)
    {
        return $this->psychometricTestRepository->createTest($data);
    }

    /**
     * Update an existing psychometric test.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\PsychometricTest
     */
    public function updateTest(int $id, array $data)
    {
        $test = PsychometricTest::findOrFail($id);

        $test->title = $data['title'];
        $test->instructions = $data['instructions'];
        $test->description = $data['description'];
        $test->duration_minutes = $data['is_timed'] ? $data['duration'] : null;
        $test->is_active = $data['is_active'];
        $test->is_timed = $data['is_timed'];
        $test->max_attempts = $data['max_attempts'];
        $test->version = $data['version'];
        $test->question_count = $data['question_count'];
        $test->psychometric_test_type_id = $data['psychometric_test_type_id'];
        $test->updated_by = Auth::id();

        return $test->save();
    }
}
