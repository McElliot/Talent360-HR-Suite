<div class="flex gap-2">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="filled" icon="ellipsis-horizontal" />
        <flux:navmenu>
            {{-- <flux:navmenu.item :href="route('admin.psychometrics.tests.edit', $test)" icon="pencil-square">
                Edit
            </flux:navmenu.item> --}}
            <flux:modal.trigger name="test-modal">
                <flux:navmenu.item wire:click="$dispatch('open-test-modal', { mode: 'edit', testId: {{ $test->id }} })"
                    icon="pencil-square">
                    Edit
                </flux:navmenu.item>
            </flux:modal.trigger>

            <flux:navmenu.item wire:click="toggleStatus({{ $test->id }})"
                icon="{{ $test->is_active ? 'x-circle' : 'check-circle' }}"
                class="{{ $test->is_active ? '!text-yellow-600 hover:!text-yellow-700' : '!text-green-600 hover:!text-green-700' }}">
                {{ $test->is_active ? 'Deactivate' : 'Activate' }}
            </flux:navmenu.item>

            <flux:navmenu.separator />
            <flux:navmenu.item :href="route('admin.psychometrics.tests.competencies.index', $test->id)" icon="eye">
                View Competences
            </flux:navmenu.item>
            <flux:navmenu.separator />

            <flux:navmenu.item wire:click="delete({{ $test->id }})" icon="trash" class="text-red-600"
                wire:confirm.prompt="Are you sure?\nType DELETE to confirm|DELETE">
                Delete
            </flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>
</div>