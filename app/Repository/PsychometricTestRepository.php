<?php

namespace App\Repository;

use App\Models\PsychometricTest;
use Illuminate\Support\Facades\Auth;

class PsychometricTestRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createTest(array $data)
    {
        return PsychometricTest::create([
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'description' => $data['description'],
            'duration_minutes' => $data['is_timed'] ? $data['duration'] : null,
            'is_active' => $data['is_active'],
            'is_timed' => $data['is_timed'],
            'max_attempts' => $data['max_attempts'],
            'version' => $data['version'],
            'question_count' => $data['question_count'],
            'psychometric_test_type_id' => $data['psychometric_test_type_id'],
            'created_by' => Auth::id(),
        ]);
    }

    public function updateTest(int $id, array $data)
    {
        $test = PsychometricTest::findOrFail($id);
        $test->update([
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'description' => $data['description'],
            'duration_minutes' => $data['is_timed'] ? $data['duration'] : null,
            'is_active' => $data['is_active'],
            'is_timed' => $data['is_timed'],
            'max_attempts' => $data['max_attempts'],
            'version' => $data['version'],
            'question_count' => $data['question_count'],
            'psychometric_test_type_id' => $data['psychometric_test_type_id'],
            'updated_by' => Auth::id(),
        ]);
        return $test;
    }
}
