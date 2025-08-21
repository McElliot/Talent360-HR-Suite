<?php

namespace App\Livewire\Admin\Psychometrics\Competency;

use App\Models\PsychometricTest;
use Livewire\Component;
use App\Livewire\Datatables\Psychometrics\TestCompetencesTable;

class Index extends Component
{
    public PsychometricTest $test;

    public function mount(PsychometricTest $test)
    {
        $this->test = $test;

        // dd($this->test->competences()->get());
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.competency.index', [
            'test' => $this->test,
        ]);
    }
}
