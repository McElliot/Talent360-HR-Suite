<?php

namespace App\Livewire\Datatables\Psychometrics;

use App\Models\PsychometricCompetence;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PsychometricTest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class CompetencesTable extends DataTableComponent
{
    protected $model = PsychometricCompetence::class;
    public PsychometricTest $test;
    public $refreshTable = false;

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
            ->setDefaultPerPage(10)
            ->setEmptyMessage('No competences found for this test')
            ->setTableWrapperAttributes([
                'class' => 'rounded-lg shadow border border-gray-200 dark:border-gray-700 no-bulk-banner'
            ])
            ->setOfflineIndicatorEnabled()
            // ->setSelectAllEnabled()
            ->setFilterLayout('slide-down')
            ->setFilterPillsEnabled()
            // ->setRefreshKeepAlive()
            ->setBulkActions([
                'activateSelected' => 'Activate',
                'deactivateSelected' => 'Deactivate',
                'updateWeights' => 'Update Weights',
                'exportSelected' => 'Export Selected',
                'deleteSelected' => 'Delete Selected',
            ])
            ->setTrAttributes(function ($row, $index) {
                // Zebra striping with alternating row colors
                $zebraClass = $index % 2 === 0
                    ? 'bg-white dark:bg-gray-800'
                    : 'bg-gray-50 dark:bg-gray-800/30';

                // Status-specific classes
                $statusClass = $row->is_active
                    ? 'hover:bg-gray-50 dark:hover:bg-gray-700'
                    : 'opacity-80 hover:bg-gray-100 dark:hover:bg-gray-700/70';

                return [
                    'class' => $zebraClass . ' ' . $statusClass,
                    'wire:key' => 'row-' . $row->id,
                ];
            })
            ->setSearchFieldAttributes([
                'class' => 'w-full pl-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition',
                'placeholder' => 'Search tests...',
            ])
            ->setPerPageFieldAttributes([
                'class' => 'block w-full p-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500',
                'default' => 25
            ]); // Keep table state on refresh
    }

    public function activateSelected()
    {
        $competenceIds = $this->getSelected();

        if (count($competenceIds) > 0) {
            // Update through pivot table if needed, or update competences directly
            $this->test->competences()->whereIn('competency_id', $competenceIds)->update([
                'is_active' => true // You might need to add this column
            ]);

            $this->clearSelected();
            $this->dispatch('show-toast', message: count($competenceIds) . ' competences activated successfully!');
        }
    }

    public function getBulkActionsTitle(): string
    {
        return ''; // Return empty string to remove the text
    }

    public function deactivateSelected()
    {
        $competenceIds = $this->getSelected();

        if (count($competenceIds) > 0) {
            $this->test->competences()->whereIn('competency_id', $competenceIds)->update([
                'is_active' => false
            ]);

            $this->clearSelected();
            $this->dispatch('show-toast', message: count($competenceIds) . ' competences deactivated successfully!');
        }
    }

    public function updateWeights()
    {
        $competenceIds = $this->getSelected();

        if (count($competenceIds) > 0) {
            // You might want to open a modal to set weights for selected items
            $this->dispatch('open-weight-modal', competenceIds: $competenceIds);
        }
    }

    public function exportSelected()
    {
        $competenceIds = $this->getSelected();

        if (count($competenceIds) > 0) {
            $competences = $this->test->competences()->whereIn('competency_id', $competenceIds)->get();

            // Implement your export logic here (CSV, PDF, etc.)
            $this->dispatch('export-competences', competences: $competences);
        }
    }

    public function deleteSelected()
    {
        $competenceIds = $this->getSelected();

        if (count($competenceIds) > 0) {
            // Detach from this test (doesn't delete the competence itself)
            $this->test->competences()->detach($competenceIds);

            $this->clearSelected();
            $this->dispatch('show-toast', message: count($competenceIds) . ' competences removed from test successfully!');
            $this->dispatch('refreshTable');
        }
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

    // Listen for refresh events
    #[On('competency-created')]
    #[On('competency-updated')]
    public function refreshTable()
    {
        $this->dispatch('refreshLivewireTable');
    }

    public function columns(): array
    {
        return [
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),

            Column::make("Code", "code")
                ->sortable()
                ->searchable(),

            Column::make("Weight")
                ->label(fn($row) => number_format($row->pivot_weight, 2))
                ->sortable(function (Builder $query, string $direction) {
                    return $query->orderBy('psychometric_competences_test.weight', $direction);
                }),

            Column::make("Actions")
                ->label(fn($row) => view('components.psychometrics.competence-actions', [
                    'test' => $this->test,
                    'competence' => $row
                ])),
        ];
    }
}
