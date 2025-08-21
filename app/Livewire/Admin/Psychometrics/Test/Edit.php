<?php

namespace App\Livewire\Admin\Psychometrics\Test;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Edit extends Component
{
    public PsychometricTest $test;
    public $psychometric_test_type_id;
    public $title;
    public $instructions;
    public $description;
    public $duration_minutes;
    public $max_attempts;
    public $is_active;

    protected $rules = [
        'psychometric_test_type_id' => 'required|exists:psychometric_test_types,id',
        'title' => 'required|string|max:255',
        'instructions' => 'nullable|string',
        'description' => 'nullable|string',
        'duration_minutes' => 'nullable|integer|min:1',
        'max_attempts' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function mount(PsychometricTest $test)
    {
        $this->test = $test;
        $this->psychometric_test_type_id = $test->psychometric_test_type_id;
        $this->title = $test->title;
        $this->instructions = $test->instructions;
        $this->description = $test->description;
        $this->duration_minutes = $test->duration_minutes;
        $this->max_attempts = $test->max_attempts;
        $this->is_active = $test->is_active;
    }

    public function save()
    {
        $this->validate();

        $this->test->update([
            'psychometric_test_type_id' => $this->psychometric_test_type_id,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'max_attempts' => $this->max_attempts,
            'is_active' => $this->is_active,
        ]);

        Toaster::success('Test updated successfully!');
        return redirect()->route('admin.psychometrics.tests.index');
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.test.edit', [
            'testTypes' => PsychometricTestType::active()
                ->orderBy('name')
                ->get()
        ]);
    }
}
