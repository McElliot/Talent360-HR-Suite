<?php

namespace App\Livewire\Admin\Psychometrics\Test;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use App\Services\PsychometricTestService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TestModal extends Component
{
    public $title;
    public $instructions;
    public $description;
    public $duration; // minutes
    public $is_active = true;
    public $is_timed = false;
    public $max_attempts;
    public $version = 1;
    public $question_count = 0;
    public $psychometric_test_type_id;
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

    /**
     * Custom validation messages
     */
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

    /**
     * Save a new test
     */
    public function save(PsychometricTestService $psychometricTestService)
    {
        $validated = $this->validate();

        // Create the test using the service
        $psychometricTestService->createTest($validated);

        $this->resetForm();
        Flux::modal('test-modal')->close();
        Toaster::success('Test created successfully!');
    }

    /**
     * Update existing test
     */
    public function update(PsychometricTestService $psychometricTestService)
    {
        $validated = $this->validate();

        // Update the test using the service
        $psychometricTestService->updateTest($this->testId, $validated);

        $this->resetForm();
        Flux::modal('test-modal')->close();
        Toaster::success('Test updated successfully!');
    }

    /**
     * Reset the form to default values
     */
    public function resetForm()
    {
        $this->reset();
        $this->testId = null;
        $this->is_active = true;
        $this->is_timed = false;
        $this->version = 1;
        $this->question_count = 0;
        $this->psychometric_test_type_id = null;
    }

    #[On('open-test-modal')]
    public function openTestModal($mode, $testId = null)
    {
        // Always reset the form first when opening the modal
        $this->resetForm();

        if ($mode === 'edit' && $testId) {
            $this->testId = $testId;
            $test = PsychometricTest::findOrFail($testId);
            $this->title = $test->title;
            $this->instructions = $test->instructions;
            $this->description = $test->description;
            $this->duration = $test->duration_minutes;
            $this->is_active = $test->is_active;
            $this->is_timed = $test->is_timed;
            $this->max_attempts = $test->max_attempts;
            $this->version = $test->version;
            $this->question_count = $test->question_count;
            $this->psychometric_test_type_id = $test->psychometric_test_type_id;
            // dd($test);
        }
    }

    /**
     * Handle modal close event
     */
    #[On('modal-closed')]
    public function onModalClosed($modalName)
    {
        if ($modalName === 'test-modal') {
            $this->resetForm();
        }
    }

    public function mount($psychometricTestTypeId = null)
    {
        $this->psychometric_test_type_id = $psychometricTestTypeId;
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.test.test-modal', [
            'testTypes' => PsychometricTestType::active()
                ->orderBy('name')
                ->get()
        ]);
    }
}
