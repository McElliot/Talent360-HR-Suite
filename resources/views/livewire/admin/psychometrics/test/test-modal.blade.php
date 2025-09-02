<div>
    <flux:modal name="test-modal" class="md:w-auto">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $testId ? 'Edit' : 'Create New' }} Psychometric Test</flux:heading>
                <flux:text class="mt-2" size="lg">{{ $testId ? 'Update' : 'Configure a new ' }} assessment test
                </flux:text>
            </div>

            <form wire:submit.prevent="{{ $testId ? 'update' : 'save' }}" class="w-full space-y-4">
                <div class="space-y-4">
                    <!-- Test Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Test Type
                            *</label>
                        <div x-data="{
                            selectedValue: @entangle('psychometric_test_type_id'),
                            tomSelectInstance: null,
                            init() {
                                this.setupTomSelect();
                            },
                            setupTomSelect() {
                                // Destroy old instance if exists
                                if (this.tomSelectInstance) {
                                    this.tomSelectInstance.destroy();
                                    this.tomSelectInstance = null;
                                }

                                this.$nextTick(() => {
                                    const select = this.$el.querySelector('select');
                                    if (select && typeof TomSelect !== 'undefined') {
                                        this.tomSelectInstance = new TomSelect(select, {
                                            create: false,
                                            placeholder: 'Select test type...',
                                            maxItems: 1,
                                            onChange: value => {
                                                this.selectedValue = value;
                                            },
                                        });

                                        // Set initial value if exists
                                        if (this.selectedValue) {
                                            setTimeout(() => {
                                                this.tomSelectInstance.setValue(this.selectedValue);
                                            }, 50);
                                        }
                                    }
                                });
                            }
                        }" x-effect="
                            if (tomSelectInstance) {
                                // whenever selectedValue changes externally, update TomSelect
                                tomSelectInstance.setValue(selectedValue || '');
                            }
                        " wire:key="test-type-select-{{ $psychometric_test_type_id }}-{{ $testId }}">
                            <select wire:ignore class="w-full">
                                <option value="">Select test type</option>
                                @foreach ($testTypes as $testType)
                                <option value="{{ $testType->id }}" {{ $psychometric_test_type_id==$testType->id ?
                                    'selected' : '' }}>
                                    {{ $testType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('psychometric_test_type_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title Field -->
                    <div>
                        <flux:input wire:model="title" label="Test Title *"
                            placeholder="e.g., Numerical Reasoning Test" />
                    </div>

                    <!-- Instructions -->
                    <div>
                        <flux:textarea wire:model="instructions" label="Instructions"
                            placeholder="Provide test instructions for candidates" rows="4" />
                    </div>

                    <!-- Descriptions -->
                    <div>
                        <flux:textarea wire:model="description" label="Descriptions"
                            placeholder="Provide test descriptions for candidates" rows="4" />
                    </div>

                    <!-- Timed Test Option -->
                    <div class="flex items-center">
                        <flux:checkbox wire:model="is_timed" id="is_timed" label="Timed Test (enforce time limits)" />
                    </div>

                    <!-- Configuration Fields -->
                    <div x-data="{ open: @entangle('is_timed') }" x-show="open" x-transition class="mt-3">
                        <flux:input wire:model="duration" type="number" label="Duration (minutes)"
                            placeholder="Enter duration in minutes" min="0" />
                        @error('duration')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <flux:input wire:model="max_attempts" type="number" label="Max Attempts"
                            placeholder="Leave empty for unlimited" min="1" />
                    </div>

                    <!-- Version -->
                    <div>
                        <flux:input wire:model="version" type="number" label="Version" placeholder="1" min="1"
                            step="1" />
                    </div>

                    <!-- Question Count -->
                    <div>
                        <flux:input wire:model="question_count" type="number" label="Question Count"
                            placeholder="Leave empty if unknown" min="0" step="1" />
                    </div>

                    <!-- Psychometric Test Type -->
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Psychometric Test Type
                            *</label>
                        <div wire:ignore>
                            <select x-data x-init="new TomSelect($el, {
                                create: false,
                                placeholder: 'Select psychometric test type...',
                                maxItems: 1
                            })" wire:model.defer="psychometric_test_type_id" class="w-full">
                                <option value="">Select or add psychometric test type</option>
                                @foreach ($psychometricTestTypes as $psychometricTestType)
                                <option value="{{ $psychometricTestType->id }}">{{ $psychometricTestType->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('psychometric_test_type_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <flux:checkbox wire:model="is_active" id="is_active" label="Active (available for use)" />
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">
                        <flux:button type="button" variant="ghost" :href="route('admin.psychometrics.tests.index')"
                            icon="x-mark" class="px-6 py-2.5">
                            Cancel
                        </flux:button>

                        <flux:button type="submit" variant="primary" icon="{{ $testId ? 'check' : 'plus' }}"
                            wire:loading.attr="disabled" class="px-6 py-2.5 cursor-pointer">
                            {{ $testId ? 'Update' : 'Create' }} Test
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
</div>