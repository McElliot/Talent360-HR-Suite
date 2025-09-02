<?php

namespace App\Livewire\Admin\Psychometrics\Question;

use App\Models\PsychometricTest;
use App\Models\PsychometricTestQuestion;
use App\Models\PsychometricCompetence;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    use WithFileUploads;

    public PsychometricTest $test;
    public $parent_question = null;
    public $question_text = '';
    public $question_number = null;
    public $answer_type = null;
    public $options  = [];
    public $score = 1.0;
    public $is_required = true;
    public $is_active = true;
    public $sort_order = 0;
    public $validation = [];
    public $media;

    // NEW: separate competencies
    public $availableCompetencies = [];
    public $selectedCompetencies = [];

    public function mount(PsychometricTest $test)
    {
        $this->test = $test;

        // Get competencies associated with this test
        $this->availableCompetencies = $test->competences()->get();

        // Initialize 2 options for choice questions
        $this->options = [
            ['option_text' => '', 'option_value' => '', 'score_weight' => 0.0, 'is_correct' => false, 'metadata' => null],
            ['option_text' => '', 'option_value' => '', 'score_weight' => 0.0, 'is_correct' => false, 'metadata' => null],
        ];
    }

    protected function rules()
    {
        return [
            'parent_question'       => ['nullable', 'string'],
            'question_text'         => ['required', 'string', 'max:1000'],
            'question_number'       => ['required', 'string', 'max:20'],
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
            'options'               => ['required_if:answer_type,radio,multiple_choice,dropdown,likert_scale', 'array', 'min:2'],
            'options.*.option_text' => [
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $types = ['radio', 'multiple_choice', 'dropdown', 'likert_scale'];
                    if (in_array($this->answer_type, $types) && empty($value)) {
                        $fail('Each option must have text.');
                    }
                }
            ],
            'options.*.option_value' => [
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $types = ['radio', 'multiple_choice', 'dropdown', 'likert_scale'];
                    if (in_array($this->answer_type, $types) && empty($value)) {
                        $fail('Each option must have a value.');
                    }
                }
            ],
            'options.*.score_weight' => 'nullable|numeric',
            'options.*.is_correct' => 'boolean',
            'options.*.score'       => 'nullable|numeric',
            'media'                 => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,docx',
            'sort_order'            => ['nullable', 'integer', 'min:0'],
            'validation'            => ['nullable', 'array'],
            'is_required'           => ['boolean'],
            'is_active'             => ['boolean'],
            'selectedCompetencies'  => ['array'],
            'selectedCompetencies.*' => ['exists:psychometric_competences,id'],
            'selectedCompetencies.*' => [
                'exists:psychometric_competences,id',
                function ($attribute, $value, $fail) {
                    // Ensure selected competencies belong to this test
                    if (!$this->availableCompetencies->contains('id', $value)) {
                        $fail('The selected competency is not available for this test.');
                    }
                }
            ],
        ];
    }

    protected function messages()
    {
        return [
            'question_text.required'        => 'Question text is required.',
            'question_number.required'      => 'Question number is required.',
            'answer_type.required'          => 'Please select the answer type.',
            'answer_type.in'                => 'The selected answer type is invalid.',
            'options.required_if'           => 'Options are required for this answer type.',
            'options.min'                   => 'At least two options are required.',
            'options.*.option_text.required_with' => 'Each option must have text.',
            'options.*.option_value.required_with' => 'Each option must have a value.',
            'media.max'                     => 'Media file size must not exceed 2MB.',
            'media.mimes'                   => 'Media must be one of: jpg, jpeg, png, pdf, docx.',
            'selectedCompetencies.*.exists' => 'One or more selected competencies are invalid.',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $question = PsychometricTestQuestion::create([
            'psychometric_test_id' => $this->test->id,
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

        if (in_array($this->answer_type, ['radio', 'multiple_choice', 'dropdown', 'likert_scale'])) {
            foreach ($this->options as $i => $option) {
                $question->options()->create([
                    'option_text'  => $option['option_text'],
                    'option_value' => $option['option_value'],
                    'score_weight' => $option['score_weight'] ?? 0.0,
                    'is_correct'   => $option['is_correct'] ?? false,
                    'sort_order'   => $i + 1,
                    'metadata'     => $option['metadata'] ?? null,
                ]);
            }
        }

        if (!empty($this->selectedCompetencies)) {
            $question->competencies()->attach(
                $this->selectedCompetencies,
                ['weight' => 1.0]
            );
        }

        if ($this->media) {
            $question->addMedia($this->media->getRealPath())
                ->usingFileName($this->media->getClientOriginalName())
                ->toMediaCollection('question-media');
        }

        Toaster::success('Question created successfully!');
        return redirect()->route('admin.psychometrics.tests.questions.index', $this->test->id);
    }

    public function render()
    {
        return view('livewire.admin.psychometrics.question.create');
    }
}
