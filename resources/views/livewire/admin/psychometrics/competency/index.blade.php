<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center transition-all duration-200">
        <div class="space-y-1">
            <flux:heading size="xl" class="font-semibold text-gray-900 dark:text-white">
                Psychometric Test Competences
            </flux:heading>
            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                Manage and administer <span class="font-semibold text-gray-700 dark:text-gray-200">{{
                    Str::title($test->title)
                    }}</span> competences
            </flux:text>
        </div>

        <!-- Create Button -->
        <flux:modal.trigger name="competency-modal">
            <flux:button variant="primary" color="indigo" icon="plus" size="sm"
                class="transition-transform hover:scale-[1.02] active:scale-[0.98]"
                wire:click="$dispatch('open-competency-modal', { mode: 'create' })">
                Create New Competence
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Table Card -->
    <div
        class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="p-6">
            <livewire:datatables.psychometrics.competences-table :show-search="true" :show-per-page="true"
                :show-column-selector="true" :show-filters="true" :show-bulk-actions="true" :test="$test" />
        </div>
    </div>

    <livewire:admin.psychometrics.competency.competency-modal :test="$test" />
</div>