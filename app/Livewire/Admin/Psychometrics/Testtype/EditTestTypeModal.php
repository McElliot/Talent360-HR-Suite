<?php

namespace App\Livewire\Admin\Psychometrics\Testtype;

use Livewire\Component;
use App\Models\PsychometricTestType;
use Masmerise\Toaster\Toaster;

class EditTestTypeModal extends Component
{
    public PsychometricTestType $testType;
    public string $name = '';
    public ?string $description = '';
    public bool $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount($testTypeId)
    {
        $this->testType = PsychometricTestType::findOrFail($testTypeId);
        $this->name = $this->testType->name;
        $this->description = $this->testType->description;
        $this->is_active = $this->testType->is_active;
    }

    public function save()
    {
        $this->validate();

        $this->testType->update([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        Toaster::success('Test type updated successfully!');
        $this->dispatch('test-type-updated');

        // Dispatch browser event to close the modal
        $this->dispatch('close-flux-modal', name: 'edit-test-type-' . $this->testType->id);
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.testtype.edit-test-type-modal');
    }
}
