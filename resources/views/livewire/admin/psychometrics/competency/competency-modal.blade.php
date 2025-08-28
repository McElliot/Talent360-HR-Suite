<div>
    <flux:modal name="competency-modal" class="md:w-auto">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $competencyId ? 'Edit' : 'Create New' }} Competence</flux:heading>
                <flux:text class="mt-2" size="lg">{{ $competencyId ? 'Update' : 'Create a new' }} assessment competence
                </flux:text>
            </div>

            <form wire:submit.prevent="{{ $competencyId ? 'update' : 'save' }}" class="w-full space-y-4">
                <div class="space-y-4">

                    <!-- Name Field -->
                    <div>
                        <flux:input wire:model="name" label="Competence Name *"
                            placeholder="e.g., Leadership, Problem Solving" />
                    </div>

                    <!-- Code Field -->
                    <div>
                        <flux:input wire:model="code" label="Code *" placeholder="e.g., LDR, PS" maxlength="10" />
                    </div>

                    <!-- Description -->
                    <div>
                        <flux:textarea wire:model="description" label="Description"
                            placeholder="Describe this competence and what it measures" rows="3" />
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <flux:input wire:model="sort_order" type="number" label="Sort Order" placeholder="Display order"
                            min="0" step="1" />
                    </div>

                    <!-- Weight -->
                    <div>
                        <flux:input wire:model="weight" type="number" label="Weight (%)" placeholder="e.g., 20" min="0"
                            max="100" step="0.01" />
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 mt-1">Defines the importance of
                            this competence in the overall assessment. Must be between 0 and 100.</flux:text>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>

                        <flux:button type="submit" variant="primary" icon="{{ $competencyId ? 'check' : 'plus' }}"
                            wire:loading.attr="disabled" class="px-6 py-2.5 cursor-pointer">
                            {{ $competencyId ? 'Update' : 'Create' }} Competence
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>
</div>