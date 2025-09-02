<div>
    <div class="relative mb-6 w-full">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="mb-2">Question Management</flux:heading>
                <flux:subheading size="lg" class="mb-6">Edit question #{{ $question->question_number }}
                </flux:subheading>
            </div>
            <div class="space-x-2">
                <flux:button variant="danger" size="sm"
                    :href="url()->previous() ?? route('admin.psychometrics.tests.questions.index', $test->id)"
                    class="shadow-sm hover:shadow-md transition-all">
                    Back
                </flux:button>
            </div>
        </div>
        <flux:separator />
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow space-y-6">
        <form wire:submit="save" class="my-6 w-full space-y-6">

            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        {{--
                        <x-icons.exclamation-circle class="h-5 w-5 text-red-500" /> --}}
                    </div>
                    <div class="ml-3">
                        <flux:heading size="sm" class="text-red-800 dark:text-red-200">
                            There were errors with your submission
                        </flux:heading>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Question Content Section -->
            <div class="space-y-6">
                <flux:heading size="lg" class="text-gray-800 dark:text-gray-200">Question Content</flux:heading>

                <!-- Parent Question (for grouped items) -->
                <flux:textarea label="Parent Question (if grouped)" wire:model="parent_question"
                    placeholder="Main question text for grouped items..." rows="2" />

                <!-- Specific Question Text -->
                <flux:textarea label="Question Text *" wire:model="question_text"
                    :invalid="$errors->has('question_text')" placeholder="Enter your specific question here..."
                    rows="3" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Answer Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Answer Type</label>
                        <div wire:ignore>
                            <select x-data x-init="new TomSelect($el, {
                                create: false,
                                placeholder: 'Select answer type',
                                maxItems: 1
                            })" wire:model="answer_type" class="w-full">
                                <option value="">Select answer type</option>
                                <option value="open_ended" {{ $answer_type=='open_ended' ? 'selected' : '' }}>Text
                                    (Single Line)</option>
                                <option value="text_area" {{ $answer_type=='text_area' ? 'selected' : '' }}>Textarea
                                    (Multi-line)</option>
                                <option value="radio" {{ $answer_type=='radio' ? 'selected' : '' }}>Single Choice
                                    (Radio)</option>
                                <option value="multiple_choice" {{ $answer_type=='multiple_choice' ? 'selected' : '' }}>
                                    Multiple Choice (Checkbox)</option>
                                <option value="dropdown" {{ $answer_type=='dropdown' ? 'selected' : '' }}>Dropdown
                                </option>
                                <option value="file_upload" {{ $answer_type=='file_upload' ? 'selected' : '' }}>File
                                    Upload</option>
                                <option value="date" {{ $answer_type=='date' ? 'selected' : '' }}>Date</option>
                                <option value="numeric" {{ $answer_type=='numeric' ? 'selected' : '' }}>Number</option>
                                <option value="likert_scale" {{ $answer_type=='likert_scale' ? 'selected' : '' }}>Likert
                                    Scale (1-5)</option>
                                <option value="true_false" {{ $answer_type=='true_false' ? 'selected' : '' }}>True /
                                    False</option>
                                <option value="matrix" {{ $answer_type=='matrix' ? 'selected' : '' }}>Matrix/Grid
                                </option>
                                <option value="ranking" {{ $answer_type=='ranking' ? 'selected' : '' }}>Ranking</option>
                            </select>
                        </div>
                        @error('answer_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Question Number -->
                    <flux:input label="Question Number *" wire:model="question_number"
                        :invalid="$errors->has('question_number')" type="text" placeholder="e.g. 1, 1a, 2b" />
                </div>

                <div x-data="{ options: $wire.entangle('options') }"
                    x-show="['radio','multiple_choice','dropdown','likert_scale'].includes($wire.answer_type)">
                    <flux:heading size="md">Response Options</flux:heading>
                    <div class="space-y-3">
                        <template x-for="(option, i) in options" :key="i">
                            <div class="flex items-start gap-3">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 flex-1">
                                    <flux:input x-bind:label="'Option Text ' + (i+1)" x-model="option.option_text"
                                        placeholder="Display text" />
                                    <flux:input x-bind:label="'Option Value ' + (i+1)" x-model="option.option_value"
                                        placeholder="Stored value" />
                                    <flux:input x-bind:label="'Score Weight ' + (i+1)" x-model="option.score_weight"
                                        type="number" step="0.01" min="0" />
                                    <label class="flex items-center space-x-2 mt-3">
                                        <input type="checkbox" x-model="option.is_correct" />
                                        <span>Correct?</span>
                                    </label>
                                </div>
                                <div class="pt-6">
                                    <button type="button" @click="options.splice(i, 1)"
                                        class="text-red-500 hover:text-red-700">
                                        <x-icons.trash class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </template>
                        <flux:button type="button"
                            @click="options.push({option_text:'',option_value:'',score_weight:0.0,is_correct:false,metadata:null})"
                            variant="outline" size="sm">
                            Add Option
                        </flux:button>
                    </div>
                </div>
            </div>

            <!-- Question Settings Section -->
            <div class="space-y-6">
                <flux:heading size="lg" class="text-gray-800 dark:text-gray-200">Question Settings</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Base Score -->
                    <flux:input label="Base Score" wire:model="score" :invalid="$errors->has('score')" type="number"
                        step="0.5" min="0" placeholder="Default score value" />

                    <!-- Required -->
                    <flux:checkbox wire:model="is_required" label="Required"
                        description="Is this question mandatory?" />

                    <!-- Active Status -->
                    <flux:checkbox wire:model="is_active" label="Active" description="Show this question in tests" />
                </div>

                <!-- Validation Rules -->
                <div x-show="!['radio','multiple_choice','dropdown','likert_scale'].includes($wire.answer_type)">
                    <flux:heading size="md" class="text-gray-700 dark:text-gray-300">Validation Rules</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input label="Minimum Length" wire:model="validation.min" type="number"
                            placeholder="Characters" />

                        <flux:input label="Maximum Length" wire:model="validation.max" type="number"
                            placeholder="Characters" />
                    </div>
                </div>

                <!-- File Upload Specific -->
                <div x-show="$wire.answer_type === 'file_upload'">
                    <flux:heading size="md" class="text-gray-700 dark:text-gray-300">File Upload Settings</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input label="Allowed File Types" wire:model="validation.file_types"
                            placeholder="pdf,docx,jpg" />

                        <flux:input label="Max File Size (MB)" wire:model="validation.file_size" type="number" />
                    </div>
                </div>
            </div>

            <!-- Competencies Section -->
            <div class="space-y-4">
                <flux:heading size="md" class="text-gray-700 dark:text-gray-300">Competencies</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($availableCompetencies as $competency)
                    <label class="flex items-center space-x-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                        <input type="checkbox" wire:model="selectedCompetencies" value="{{ $competency->id }}">
                        <span class="text-sm">{{ $competency->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>



            <div class="pt-6 border-t border-gray-200 dark:border-gray-800 flex justify-end space-x-4">
                <flux:button type="button" variant="ghost"
                    :href="route('admin.psychometrics.tests.questions.index', $test->id)" icon="x-mark"
                    class="px-6 py-2.5 shadow-sm hover:shadow transition-all">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check"
                    class="px-6 py-2.5 shadow-sm hover:shadow transition-all">
                    Update Question
                </flux:button>
            </div>
        </form>
    </div>
</div>