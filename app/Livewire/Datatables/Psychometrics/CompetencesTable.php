<?php

namespace App\Livewire\Datatables\Psychometrics;

use App\Models\PsychometricCompetence;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PsychometricTest;
use Illuminate\Database\Eloquent\Builder;

class CompetencesTable extends DataTableComponent
{
    protected $model = PsychometricCompetence::class;
    public PsychometricTest $test;

    public function mount(PsychometricTest $test)
    {
        $this->test = $test;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('sort_order', 'asc')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setEmptyMessage('No competences found for this test');
    }

    public function builder(): Builder
    {
        return PsychometricCompetence::query()
            ->join('psychometric_competences_test', 'psychometric_competences.id', '=', 'psychometric_competences_test.competency_id')
            ->where('psychometric_competences_test.test_id', $this->test->id)
            ->select([
                'psychometric_competences.*',
                'psychometric_competences_test.weight as pivot_weight'
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Name", "name")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => $value
                ),

            Column::make("Code", "code")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => $value
                ),

            Column::make("Weight", "pivot_weight") // Use the aliased column name
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) => number_format($value, 2)
                ),

            Column::make("Actions")
                ->label(
                    fn($row, Column $column) => view('components.psychometrics.competence-actions', [
                        'test' => $this->test,
                        'competence' => $row
                    ])
                )
                ->unclickable(),
        ];
    }
}
