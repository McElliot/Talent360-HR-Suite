<?php

namespace App\Livewire\Admin\Psychometrics\Test;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestType;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $psychometric_test_type_id;
    public $title;
    public $instructions;
    public $description;
    public $duration_minutes;
    public $max_attempts;
    public $is_active = true;

    protected $rules = [
        'psychometric_test_type_id' => 'required|exists:psychometric_test_types,id',
        'title' => 'required|string|max:255',
        'instructions' => 'nullable|string',
        'description' => 'nullable|string',
        'duration_minutes' => 'nullable|integer|min:1',
        'max_attempts' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        $test = PsychometricTest::create([
            'psychometric_test_type_id' => $this->psychometric_test_type_id,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'max_attempts' => $this->max_attempts,
            'is_active' => $this->is_active,
            // created_by and version set automatically in model boot
        ]);

        Toaster::success('Test created successfully!');
        return redirect()->route('admin.psychometrics.tests.index', $test);
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
