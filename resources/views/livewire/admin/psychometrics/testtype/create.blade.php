<div class="space-y-6">
    <!-- Header -->
    <div class="relative mb-6 w-full">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Larger heading for better hierarchy -->
                <flux:heading size="2xl" class="text-2xl font-semibold text-gray-900 dark:text-white">
                    Create New Test Type
                </flux:heading>
                <flux:subheading size="lg" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add a new psychometric assessment type to your system
                </flux:subheading>
            </div>
            <div class="space-x-2">
                <!-- Back button with clearer label -->
                <flux:button variant="danger" size="sm" :href="route('admin.psychometrics.index')"
                    icon="arrow-uturn-left" class="shadow-sm hover:shadow-md transition-all hover:bg-red-600">
                    Back to Test Types
                </flux:button>
            </div>
        </div>
        <flux:separator class="mt-4" />
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow space-y-6">
        <!-- Form -->
        <form wire:submit.prevent="save" class="w-full space-y-4">
            <div class="space-y-4">
                <!-- Name Field (Required) -->
                <div>
                    <flux:input wire:model="name" label="Test Type Name *" placeholder="e.g., Cognitive Ability"
                        autofocus class="focus:ring-2 focus:ring-primary-500" />
                </div>

                <!-- Description Field -->
                <div>
                    <flux:textarea wire:model="description" label="Description"
                        placeholder="Optional description (e.g., 'Measures logical reasoning and problem-solving')"
                        rows="4" class="focus:ring-2 focus:ring-primary-500" />
                </div>

                <!-- Active Status Toggle -->
                <div class="flex items-center">
                    <flux:checkbox wire:model="is_active" id="is_active" class="rounded focus:ring-primary-500"
                        label="Active (available for use)" />
                </div>

                <!-- Form Actions -->
                <div
                    class="pt-6 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">
                    <!-- Cancel/Discard Button -->
                    <flux:button type="button" variant="ghost" wire:click="cancel" icon="x-mark"
                        class="px-6 py-2.5 shadow-sm hover:shadow transition-all hover:bg-gray-100 dark:hover:bg-gray-800">
                        Discard Changes
                    </flux:button>

                    <!-- Primary Submit Button -->
                    <flux:button type="submit" variant="primary" :loading icon="plus" wire:loading.attr="disabled"
                        class="px-6 py-2.5 shadow-sm hover:shadow-md transition-all bg-primary-600 hover:bg-primary-700 disabled:opacity-70">
                        Create Test Type
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</div>