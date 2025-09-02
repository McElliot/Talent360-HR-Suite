<div class="space-y-6">
    <!-- Header -->
    <div class="relative mb-6 w-full">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="2xl" class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Create New Psychometric Test
                </flux:heading>
                <flux:subheading size="lg" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Configure a new assessment test
                </flux:subheading>
            </div>
            <div class="space-x-2">
                <flux:button variant="danger" size="sm" :href="route('admin.psychometrics.tests.index')"
                    icon="arrow-uturn-left" class="shadow-sm hover:shadow-md transition-all hover:bg-red-600">
                    Back to Tests
                </flux:button>
            </div>
        </div>
        <flux:separator class="mt-4" />
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow space-y-6">
        <form wire:submit.prevent="save" class="w-full space-y-4">
            <div class="space-y-4">
                <!-- Test Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Test Type *</label>
                    <div wire:ignore>
                        <select x-data x-init="new TomSelect($el, {
                                                                    create: false,
                                                                    placeholder: 'Select test type...',
                                                                    maxItems: 1
                                                                })" wire:model.defer="psychometric_test_type_id"
                            class="w-full">
                            <option value="">Select or add category</option>
                            @foreach ($testTypes as $testType)
                            <option value="{{ $testType->id }}">{{ $testType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('psychometric_test_type_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title Field -->
                <div>
                    <flux:input wire:model="title" label="Test Title *" placeholder="e.g., Numerical Reasoning Test"
                        autofocus />
                </div>

                <!-- Instructions -->
                <div>
                    <flux:textarea wire:model="instructions" label="Instructions"
                        placeholder="Provide test instructions for candidates" rows="4" />
                </div>

                <!-- Description -->
                <div>
                    <flux:textarea wire:model="description" label="Descriptions"
                        placeholder="Provide test descriptions for candidates" rows="4" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <flux:checkbox wire:model="is_active" id="is_active" label="Active (available for use)" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">
                    <flux:button type="button" variant="ghost" :href="route('admin.psychometrics.tests.index')"
                        icon="x-mark" class="px-6 py-2.5">
                        Cancel
                    </flux:button>

                    <flux:button type="submit" variant="primary" icon="plus" wire:loading.attr="disabled"
                        class="px-6 py-2.5">
                        Create Test
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>