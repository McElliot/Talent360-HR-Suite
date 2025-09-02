<?php

namespace App\Livewire\Admin\Psychometrics\Test;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use App\Services\PsychometricTestService;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $psychometric_test_type_id;
    public $title;
    public $instructions;
    public $description;
    public $max_attempts;
    public $is_active = true;
    public $duration; // minutes
    public $is_timed = false;
    public $version = 1;
    public $question_count = 0;
    public $testId = null;

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'duration' => $this->is_timed ? 'required|integer|min:1' : 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_timed' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'version' => 'required|integer|min:1',
            'question_count' => 'required|integer|min:1',
            'psychometric_test_type_id' => 'required|exists:psychometric_test_types,id',
        ];
    }

    protected $messages = [
        'title.required' => 'A test title is required.',
        'title.max' => 'The title may not be greater than 255 characters.',
        'duration.required' => 'Duration is required when the test is timed.',
        'duration.integer' => 'Duration must be a number (in minutes).',
        'duration.min' => 'Duration must be at least 1 minute.',
        'version.required' => 'Please specify the version of the test.',
        'question_count.required' => 'Please enter the total number of questions.',
        'psychometric_test_type_id.required' => 'Please select a psychometric test type.',
        'psychometric_test_type_id.exists' => 'Invalid test type selected.',
    ];

    public function save(PsychometricTestService $psychometricTestService)
    {
        $this->validate();

        $validated = $this->validate();

        // Create the test using the service
        $psychometricTestService->createTest($validated);

        Toaster::success('Test created successfully!');
        return redirect()->route('admin.psychometrics.tests.index');
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.test.create', [
            'testTypes' => PsychometricTestType::active()
                ->orderBy('name')
                ->get()
        ]);
    }
}
