<?php

namespace App\Livewire\Admin\Psychometrics\Testtype;

use App\Models\PsychometricTestType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public string $name = '';
    public ?string $description = '';
    public bool $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255|unique:psychometric_test_types,name',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        $testType = PsychometricTestType::create([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        Toaster::success('Test type created successfully!');
        $this->redirect(route('admin.psychometrics.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.testtype.create');
    }
}
