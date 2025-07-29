<?php

namespace App\Livewire\Datatables\Psychometrics;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PsychometricTestType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\HtmlString;

class TestTypeTable extends DataTableComponent
{
    protected $model = PsychometricTestType::class;
    protected $listeners = ['test-type-updated' => '$refresh'];

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
            ->setEmptyMessage('No test types found')
            ->setBulkActionsEnabled();
    }

    public function toggleStatus($id)
    {
        $testType = PsychometricTestType::findOrFail($id);
        $testType->update(['is_active' => !$testType->is_active]);
    }

    public function delete($id)
    {
        PsychometricTestType::findOrFail($id)->delete();
    }

    public function bulkActions(): array
    {
        return [
            'activateSelected' => 'Activate',
            'deactivateSelected' => 'Deactivate',
            'deleteSelected' => 'Delete',
        ];
    }

    public function activateSelected()
    {
        PsychometricTestType::whereIn('id', $this->getSelected())->update(['is_active' => true]);
        $this->clearSelected();
    }

    public function deactivateSelected()
    {
        PsychometricTestType::whereIn('id', $this->getSelected())->update(['is_active' => false]);
        $this->clearSelected();
    }

    public function deleteSelected()
    {
        PsychometricTestType::whereIn('id', $this->getSelected())->delete();
        $this->clearSelected();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->searchable()
                ->sortable()
                ->format(fn($value) => '<span class="font-mono">' . $value . '</span>')
                ->html(),

            Column::make("Name", "name")
                ->searchable()
                ->sortable()
                ->format(
                    fn($value, $row) => view('components.datatables.test-type-name', [
                        'name' => $value,
                        'active' => $row->is_active
                    ])
                ),

            Column::make("Description", "description")
                ->searchable()
                ->collapseOnMobile()
                ->format(fn($value) => Str::limit($value, 50)),

            Column::make("Created By", "createdBy.name")
                ->searchable()
                ->collapseOnTablet(),

            BooleanColumn::make("Active", "is_active")
                ->sortable()
                ->setView('components.datatables.boolean-status'),

            Column::make("Created", "created_at")
                ->sortable()
                ->format(fn($value) => $value->format('M d, Y'))
                ->collapseOnMobile(),

            Column::make('Actions')
                ->label(fn($row) => new HtmlString(
                    view('components.datatables.test-type-table-actions', ['type' => $row])->render()
                ))
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Active Status', 'is_active')
                ->options([
                    '' => 'All',
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('is_active', $value);
                }),

            DateRangeFilter::make('Created Between')
                ->filter(function (Builder $builder, array $dateRange) {
                    if ($start = Arr::get($dateRange, 'minDate')) {
                        $builder->where('tests.created_at', '>=', Carbon::parse($start)->startOfDay());
                    }
                    if ($end = Arr::get($dateRange, 'maxDate')) {
                        $builder->where('tests.created_at', '<=', Carbon::parse($end)->endOfDay());
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        return PsychometricTestType::query()
            ->with('createdBy')
            ->when(!Auth::user()->isAdmin(), function ($query) {
                return $query->where('is_active', true);
            });
    }
}
