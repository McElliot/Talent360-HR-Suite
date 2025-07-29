<div class="space-y-6">
    <!-- Header Section with improved spacing and subtle animation -->
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center transition-all duration-200">
        <div class="space-y-1">
            <flux:heading size="xl" class="font-semibold text-gray-900 dark:text-white">
                Psychometric Test Types
            </flux:heading>
            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                Manage and organize all available assessment types
            </flux:text>
        </div>

        <!-- Create Button with modern styling -->
        <flux:button variant="primary" icon="plus" size="sm" :href="route('admin.psychometrics.create')"
            class="transition-transform hover:scale-[1.02] active:scale-[0.98]">
            Create Test Type
        </flux:button>
    </div>

    <!-- Table Card with modern shadow and border -->
    <div
        class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm transition-all duration-200 hover:shadow-md">
        <div class="p-6">
            <livewire:datatables.psychometrics.test-type-table :show-search="true" :show-per-page="true"
                :show-column-selector="true" :show-filters="true" />
        </div>
    </div>

    @push('scripts')
    <script type="module">
        // Modern event listener with cleanup
        const handleCloseModal = ({ detail: { name } }) => {
            const modal = document.querySelector(`flux-modal[name="${name}"]`);

            if (modal) {
                // Modern approach using Flux's JS API if available
                if (window.Flux?.modal?.close) {
                    window.Flux.modal.close(name);
                } else {
                    // Fallback to DOM approach
                    const closeBtn = modal.querySelector('[data-modal-close]');
                    closeBtn?.click();
                }
            }
        };

        Livewire.on('close-modal', handleCloseModal);

        // Cleanup when Livewire component is removed
        document.addEventListener('livewire:destroy', () => {
            Livewire.off('close-modal', handleCloseModal);
        });
    </script>
    @endpush
</div>