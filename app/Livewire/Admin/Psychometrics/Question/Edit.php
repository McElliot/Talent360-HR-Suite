<?php

namespace App\Livewire\Admin\Psychometrics\Question;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestQuestion;
use App\Models\PsychometricCompetence;
use App\Models\PsychometricQuestionOption;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Edit extends Component
{
    use WithFileUploads;

    public PsychometricTest $test;
    public PsychometricTestQuestion $question;
    public $parent_question;
    public $question_text;
    public $question_number;
    public $answer_type;
    public $options = [];
    public $score;
    public $is_required;
    public $is_active;
    public $sort_order;
    public $validation = [];
    public $media;

    public $availableCompetencies = [];
    public $selectedCompetencies = [];

    public function mount(PsychometricTest $test, PsychometricTestQuestion $question)
    {
        $this->test = $test;
        $this->question = $question;

        // Populate form fields with existing data
        $this->parent_question = $question->parent_question;
        $this->question_text = $question->question_text;
        $this->question_number = $question->question_number;
        $this->answer_type = $question->answer_type;
        $this->score = $question->score;
        $this->is_required = $question->is_required;
        $this->is_active = $question->is_active;
        $this->sort_order = $question->sort_order;
        $this->validation = $question->validation ?? [];

        // Load existing options if applicable
        if (in_array($this->answer_type, ['radio', 'multiple_choice', 'dropdown', 'likert_scale'])) {
            $this->options = $question->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'option_value' => $option->option_value,
                    'score_weight' => $option->score_weight,
                    'is_correct' => $option->is_correct,
                    'metadata' => $option->metadata
                ];
            })->toArray();
        }

        // Get competencies associated with this test
        $this->availableCompetencies = $test->competences()->get();

        // Get selected competencies for this question
        $this->selectedCompetencies = $question->competencies->pluck('id')->toArray();
    }

    protected function rules()
    {
        $rules = [
            'parent_question'       => ['nullable', 'string'],
            'question_text'         => ['required', 'string', 'max:1000'],
            'question_number'       => [
                'required',
                'string',
                'max:20',
                Rule::unique('psychometric_test_questions')
                    ->where('psychometric_test_id', $this->test->id)
                    ->ignore($this->question->id)
            ],
            'answer_type'           => ['required', Rule::in([
                'open_ended',
                'text_area',
                'radio',
                'multiple_choice',
                'dropdown',
                'file_upload',
                'date',
                'numeric',
                'likert_scale',
                'true_false',
                'matrix',
                'ranking'
            ])],
            'media'                 => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,docx'],
            'sort_order'            => ['nullable', 'integer', 'min:0'],
            'validation'            => ['nullable', 'array'],
            'is_required'           => ['boolean'],
            'is_active'             => ['boolean'],
            'selectedCompetencies'  => ['array'],
            'selectedCompetencies.*' => [
                'exists:psychometric_competences,id',
                function ($attribute, $value, $fail) {
                    if (!$this->availableCompetencies->contains('id', $value)) {
                        $fail('The selected competency is not available for this test.');
                    }
                }
            ],
        ];

        // Only validate options if the answer type requires them
        if (in_array($this->answer_type, ['radio', 'multiple_choice', 'dropdown', 'likert_scale'])) {
            $rules['options'] = ['required', 'array', 'min:2'];
            $rules['options.*.option_text'] = ['required', 'string', 'min:1'];
            $rules['options.*.option_value'] = ['nullable', 'string'];
            $rules['options.*.score_weight'] = ['nullable', 'numeric'];
            $rules['options.*.is_correct'] = ['boolean'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'question_text.required'        => 'Question text is required.',
            'question_number.required'      => 'Question number is required.',
            'question_number.unique'        => 'This question number already exists for this test.',
            'answer_type.required'          => 'Please select the answer type.',
            'answer_type.in'                => 'The selected answer type is invalid.',
            'options.required'              => 'Options are required for this answer type.',
            'options.min'                   => 'At least two options are required.',
            'options.*.option_text.required' => 'Each option must have text.',
            'media.max'                     => 'Media file size must not exceed 2MB.',
            'media.mimes'                   => 'Media must be one of: jpg, jpeg, png, pdf, docx.',
            'selectedCompetencies.*.exists' => 'One or more selected competencies are invalid.',
        ];
    }

    public function updatedAnswerType($value)
    {
        $optionRequiredTypes = ['radio', 'multiple_choice', 'dropdown', 'likert_scale'];

        // Clear options when switching to a non-option answer type
        if (!in_array($value, $optionRequiredTypes)) {
            $this->options = [];
        } else if (empty($this->options)) {
            // Initialize options when switching to an option-based answer type
            $this->options = [
                ['option_text' => '', 'option_value' => '', 'score_weight' => 0.0, 'is_correct' => false, 'metadata' => null],
                ['option_text' => '', 'option_value' => '', 'score_weight' => 0.0, 'is_correct' => false, 'metadata' => null]
            ];
        }
    }

    public function save()
    {
        $this->validate();

        // Update the question
        $this->question->update([
            'question_number'      => $this->question_number,
            'parent_question'      => $this->parent_question,
            'question_text'        => $this->question_text,
            'answer_type'          => $this->answer_type,
            'is_required'          => $this->is_required,
            'validation'           => $this->validation,
            'sort_order'           => $this->sort_order,
            'score'                => $this->score,
            'is_active'            => $this->is_active,
        ]);

        // Handle options for question types that require them
        if (in_array($this->answer_type, ['radio', 'multiple_choice', 'dropdown', 'likert_scale'])) {
            // Get existing option IDs to track which ones to delete
            $existingOptionIds = $this->question->options->pluck('id')->toArray();
            $updatedOptionIds = [];

            // Update or create options
            foreach ($this->options as $i => $optionData) {
                if (isset($optionData['id'])) {
                    // Update existing option
                    $option = PsychometricQuestionOption::find($optionData['id']);
                    if ($option) {
                        $option->update([
                            'option_text'  => $optionData['option_text'],
                            'option_value' => $optionData['option_value'] ?? $optionData['option_text'],
                            'score_weight' => $optionData['score_weight'] ?? 0.0,
                            'is_correct'   => $optionData['is_correct'] ?? false,
                            'sort_order'   => $i + 1,
                            'metadata'     => $optionData['metadata'] ?? null,
                        ]);
                        $updatedOptionIds[] = $option->id;
                    }
                } else {
                    // Create new option
                    $option = $this->question->options()->create([
                        'option_text'  => $optionData['option_text'],
                        'option_value' => $optionData['option_value'] ?? $optionData['option_text'],
                        'score_weight' => $optionData['score_weight'] ?? 0.0,
                        'is_correct'   => $optionData['is_correct'] ?? false,
                        'sort_order'   => $i + 1,
                        'metadata'     => $optionData['metadata'] ?? null,
                    ]);
                    $updatedOptionIds[] = $option->id;
                }
            }

            // Delete options that were removed
            $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
            if (!empty($optionsToDelete)) {
                PsychometricQuestionOption::whereIn('id', $optionsToDelete)->delete();
            }
        } else {
            // Delete all options if answer type changed to non-option type
            $this->question->options()->delete();
        }

        // Sync competencies
        $this->question->competencies()->sync(
            collect($this->selectedCompetencies)->mapWithKeys(function ($competencyId) {
                return [$competencyId => ['weight' => 1.0]];
            })
        );

        // Handle media upload
        if ($this->media) {
            // Remove existing media
            $this->question->clearMediaCollection('question-media');

            // Add new media
            $this->question->addMedia($this->media->getRealPath())
                ->usingFileName($this->media->getClientOriginalName())
                ->toMediaCollection('question-media');
        }

        Toaster::success('Question updated successfully!');
        return redirect()->route('admin.psychometrics.tests.questions.index', $this->test->id);
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.question.edit');
    }
}
