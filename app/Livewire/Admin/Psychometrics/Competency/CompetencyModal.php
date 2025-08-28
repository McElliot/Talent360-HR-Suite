<?php

namespace App\Livewire\Admin\Psychometrics\Competency;

use Livewire\Component;
use App\Models\PsychometricCompetence;
use App\Models\PsychometricTestType;
use App\Services\PsychometricTestCompetenceService;
use Flux\Flux;
use Livewire\Attributes\On;
use Masmerise\Toaster\Toaster;

class CompetencyModal extends Component
{
    public $competencyId = null;
    public $test;

    public $name;
    public $code;
    public $description;
    public $test_type_id;
    public $sort_order = 0;
    public $weight = 1.0;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:psychometric_competences,code,' . $this->competencyId,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
        ];
    }

    public function save(PsychometricTestCompetenceService $competenceService)
    {
        $validated = $this->validate();
        $validated['test_type_id'] = $this->test->psychometric_test_type_id;
        $validated['test_id'] = $this->test->id ?? null;

        $competence = $competenceService->createCompetence($validated);
        $this->resetForm();
        Flux::modal('competency-modal')->close();
        Toaster::success('Competency created successfully.');

        // Dispatch event to refresh the table
        $this->dispatch('competency-created');
    }

    public function update(PsychometricTestCompetenceService $competenceService)
    {
        $validated = $this->validate();
        $validated['test_type_id'] = $this->test->psychometric_test_type_id;
        $validated['test_id'] = $this->test->id ?? null;

        $competenceService->updateCompetence($this->competencyId, $validated);

        $this->resetForm();
        Flux::modal('competency-modal')->close();
        Toaster::success('Competency updated successfully.');

        // Dispatch event to refresh the table
        $this->dispatch('competency-updated');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'code',
            'description',
            'test_type_id',
            'sort_order',
            'weight',
        ]);
        $this->sort_order = 0;
        $this->weight = 1.0;
        $this->test_type_id = PsychometricTestType::first()?->id ?? null;
        $this->competencyId = null;
    }

    #[On('open-competency-modal')]
    public function openCompetencyModal($mode, $competencyId = null)
    {
        $this->resetForm();

        if ($mode === 'edit' && $competencyId) {
            $this->competencyId = $competencyId;
            $competence = PsychometricCompetence::find($competencyId);

            if (!$competence) {
                Toaster::error('Competency not found.');
                return;
            }

            $this->name = $competence->name;
            $this->code = $competence->code;
            $this->description = $competence->description;
            $this->test_type_id = $competence->test_type_id;
            $this->sort_order = $competence->sort_order;
            $this->weight = $competence->tests()->where('test_id', $this->test->id)->first()?->pivot->weight ?? 1.0;
        }
    }

    public function mount()
    {
        $this->test_type_id = PsychometricTestType::first()?->id ?? null;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.competency.competency-modal');
    }
}
