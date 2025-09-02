<?php

namespace App\Livewire\Datatables\Psychometrics;

use App\Models\PsychometricTest;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PsychometricTestQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class QuestionsTable extends DataTableComponent
{
    public $testId;

    protected $model = PsychometricTestQuestion::class;
    public PsychometricTest $test;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('sort_order', 'asc')
            ->setFilterLayoutSlideDown()
            ->setRememberColumnSelectionDisabled()
            ->setSecondaryHeaderTrAttributes(function ($rows) {
                return ['class' => 'bg-gray-50 dark:bg-gray-800'];
            })
            ->setSecondaryHeaderTdAttributes(function (Column $column, $rows) {
                if ($column->isField('id')) {
                    return ['class' => 'text-red-500'];
                }
                return ['default' => true];
            });
    }

    public function mount(): void
    {
        $this->testId = $this->test->id;
    }

    public function builder(): Builder
    {
        return PsychometricTestQuestion::query()
            ->where('psychometric_test_id', $this->testId)
            ->with(['options', 'competencies'])
            ->select(['*', DB::raw('parent_question')]);
    }

    public function columns(): array
    {
        return [
            Column::make("Order", "question_number")
                ->sortable()
                ->collapseOnMobile(),

            Column::make("Question", "question_text")
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) =>
                    view('livewire.admin.psychometrics.question.partials.question-text')
                        ->with('question', $row)
                ),

            Column::make("Type", "answer_type")
                ->sortable()
                ->format(
                    fn($value, $row, Column $column) =>
                    '<span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">'
                        . str_replace('_', ' ', $value)
                        . '</span>'
                )->html(),

            BooleanColumn::make("Required", "is_required")
                ->sortable(),

            BooleanColumn::make("Active", "is_active")
                ->sortable(),

            Column::make("Options", "id")
                ->format(
                    fn($value, $row, Column $column) =>
                    $row->options->count() > 0 ? $row->options->count() . ' options'
                        : 'No options'
                )->collapseOnTablet(),

            Column::make("Competencies", "id")
                ->format(
                    fn($value, $row, Column $column) =>
                    $row->competencies->count() > 0
                        ? $row->competencies->count() . ' competencies'
                        : 'No competencies'
                )->collapseOnTablet(),

            Column::make("Actions")
                ->label(
                    fn($row, Column $column) => view('components.datatables.questions-actions', ['question' => $row])
                ),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Question Type')
                ->options([
                    '' => 'All',
                    'multiple_choice' => 'Multiple Choice',
                    'radio' => 'Single Select',
                    'open_ended' => 'Open Ended',
                    'likert_scale' => 'Likert Scale',
                    'true_false' => 'True/False',
                    'dropdown' => 'Dropdown',
                    'matrix' => 'Matrix',
                    'ranking' => 'Ranking',
                    'date' => 'Date',
                    'file_upload' => 'File Upload',
                    'numeric' => 'Numeric',
                    'text_area' => 'Text Area',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('answer_type', $value);
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'active') {
                        $builder->where('is_active', true);
                    } elseif ($value === 'inactive') {
                        $builder->where('is_active', false);
                    }
                }),
        ];
    }

    // Add toggle methods
    public function toggleRequired($questionId): void
    {
        $question = PsychometricTestQuestion::find($questionId);
        if ($question) {
            $question->update(['is_required' => !$question->is_required]);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Question requirement status updated successfully.'
            ]);
        }
    }

    public function toggleStatus($questionId): void
    {
        $question = PsychometricTestQuestion::find($questionId);
        if ($question) {
            $question->update(['is_active' => !$question->is_active]);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Question status updated successfully.'
            ]);
        }
    }

    public function delete($questionId): void
    {
        $question = PsychometricTestQuestion::find($questionId);
        if ($question) {
            $question->delete();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Question deleted successfully.'
            ]);
        }
    }
}
