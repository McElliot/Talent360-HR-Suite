<?php

namespace App\Livewire\Datatables\Psychometrics;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PsychometricTest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Str;
use Masmerise\Toaster\Toaster;

class TestTable extends DataTableComponent
{
    protected $model = PsychometricTest::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setPerPageAccepted([10, 25, 50, 100])
            ->setPerPage(10)
            ->setTableWrapperAttributes([
                'class' => 'rounded-lg shadow border border-gray-200 dark:border-gray-700'
            ])
            ->setOfflineIndicatorEnabled()
            ->setEmptyMessage('No tests found')
            ->setSelectAllEnabled()
            ->setBulkActionsEnabled()
            ->setHideBulkActionsWhenEmptyEnabled()
            ->setDefaultPerPage(10)
            ->setFilterLayout('slide-down')
            ->setFilterPillsEnabled()
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
            ]);;
    }

    public function columns(): array
    {
        return [
            Column::make('#')
                ->label(function ($row, Column $column) {
                    return ($this->getRows()->currentPage() - 1) * $this->getRows()->perPage() + $column->getRowIndex() + 1;
                }),

            Column::make("Title", "title")
                ->searchable()
                ->sortable()
                ->format(
                    fn($value, $row) => view('components.datatables.test-title', [
                        'title' => $value,
                        'active' => $row->is_active
                    ])
                ),

            Column::make("Type", "psychometricTestType.name")
                ->searchable()
                ->sortable(),

            Column::make("Description", "description")
                ->searchable()
                ->sortable()  // Enable sorting
                ->collapseOnMobile()
                ->format(fn($value) => Str::limit($value, 50)),

            Column::make("Duration", "duration_minutes")
                ->sortable()
                ->format(fn($value) => $value ? $value . ' mins' : 'No limit'),

            Column::make("Questions", "question_count")
                ->sortable(),

            BooleanColumn::make("Active", "is_active")
                ->sortable()
                ->setView('components.datatables.boolean-status'),

            Column::make("Created", "created_at")
                ->sortable()
                ->format(fn($value) => $value->format('M d, Y'))
                ->collapseOnMobile(),

            Column::make('Actions')
                ->label(fn($row) => view('components.datatables.test-actions', ['test' => $row]))
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status', 'is_active')
                ->options([
                    '' => 'All',
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('psychometric_tests.is_active', (bool)$value);
                    }
                }),

            SelectFilter::make('Test Type', 'psychometric_test_type_id')
                ->options(
                    \App\Models\PsychometricTestType::pluck('name', 'id')
                        ->prepend('All', '')
                        ->toArray()
                )
                ->filter(function (Builder $query, $value) {
                    if (!empty($value)) {
                        $query->where('psychometric_tests.psychometric_test_type_id', $value);
                    }
                }),
            DateRangeFilter::make('Created Between')
                ->filter(function (Builder $builder, array $dateRange) {
                    if ($start = Arr::get($dateRange, 'minDate')) {
                        $builder->where('psychometric_tests.created_at', '>=', Carbon::parse($start)->startOfDay());
                    }
                    if ($end = Arr::get($dateRange, 'maxDate')) {
                        $builder->where('psychometric_tests.created_at', '<=', Carbon::parse($end)->endOfDay());
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return PsychometricTest::query()
            ->select('psychometric_tests.*')
            ->with('psychometricTestType')
            // ->when(!Auth::user()->isAdmin(), function ($query) {
            //     return $query->where('psychometric_tests.is_active', true);
            // });
        ;
    }

    public function bulkActions(): array
    {
        return [
            'exportSelected' => 'Export Selected',
            'deleteSelected' => 'Delete Selected',
            'activateSelected' => 'Activate Selected',
            'deactivateSelected' => 'Deactivate Selected',
        ];
    }

    public function activateSelected()
    {
        if (count($this->getSelected()) === 0) {
            Toaster::warning('Please select at least one test');
            return;
        }

        PsychometricTest::whereIn('id', $this->getSelected())
            ->update(['is_active' => true]);

        $this->clearSelected();
        Toaster::success('Selected tests activated');
    }

    public function deactivateSelected()
    {
        if (count($this->getSelected()) === 0) {
            Toaster::warning('Please select at least one test');
            return;
        }

        PsychometricTest::whereIn('id', $this->getSelected())->update(['is_active' => false]);
        $this->clearSelected();
        Toaster::success('Selected tests deactivated');
    }

    public function exportSelected()
    {
        $selected = $this->getSelected();
        if (count($selected) < 1) {
            Toaster::warning('Please select at least one test to export');
            return;
        }

        $this->clearSelected();
        // Implement your export logic here
        // return Excel::download(...);
    }

    public function deleteSelected()
    {
        if (count($this->getSelected()) === 0) {
            Toaster::warning('Please select at least one test');
            return;
        }

        PsychometricTest::whereIn('id', $this->getSelected())->delete();
        $this->clearSelected();
        Toaster::success('Selected tests deleted');
    }

    public function toggleStatus($id)
    {
        $test = PsychometricTest::findOrFail($id);
        $test->update(['is_active' => !$test->is_active]);
        Toaster::success($test->is_active ? 'Test activated' : 'Test deactivated');
    }
}
