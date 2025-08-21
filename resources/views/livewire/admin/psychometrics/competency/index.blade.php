<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center transition-all duration-200">
        <div class="space-y-1">
            <flux:heading size="xl" class="font-semibold text-gray-900 dark:text-white">
                Psychometric Tests
            </flux:heading>
            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                Manage and administer all assessment tests
            </flux:text>
        </div>

        <!-- Create Button -->
        <flux:button variant="primary" icon="plus" size="sm" :href="route('admin.psychometrics.tests.create')"
            class="transition-transform hover:scale-[1.02] active:scale-[0.98]">
            Create New Test
        </flux:button>
    </div>

    <!-- Table Card -->
    <div
        class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="p-6">
            <livewire:datatables.psychometrics.competences-table :show-search="true" :show-per-page="true"
                :show-column-selector="true" :show-filters="true" :show-bulk-actions="true" />
        </div>
    </div>
</div>