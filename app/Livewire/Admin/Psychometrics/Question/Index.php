<?php

namespace App\Livewire\Admin\Psychometrics\Question;

use App\Models\PsychometricTest;
use Livewire\Component;

class Index extends Component
{
    public PsychometricTest $test;

    public function mount(PsychometricTest $test): void
    {
        $this->test = $test;
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.question.index', [
            'test' => $this->test,
        ]);
    }
}
