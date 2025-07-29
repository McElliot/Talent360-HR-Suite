<!-- resources/views/components/datatables/test-type-table-actions.blade.php -->
<div class="flex gap-2">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="filled" icon="ellipsis-horizontal" />
        <flux:navmenu>
            <flux:modal.trigger name="edit-test-type-{{ $type->id }}">
                <flux:navmenu.item icon="pencil-square">
                    Edit
                </flux:navmenu.item>
            </flux:modal.trigger>

            <flux:navmenu.item wire:click="toggleStatus({{ $type->id }})"
                icon="{{ $type->is_active ? 'x-circle' : 'check-circle' }}"
                class="{{ $type->is_active ? '!text-yellow-600 hover:!text-yellow-700' : '!text-green-600 hover:!text-green-700' }}">
                {{ $type->is_active ? 'Deactivate' : 'Activate' }}
            </flux:navmenu.item>

            <flux:navmenu.separator />
            <flux:navmenu.item :href="route('admin.psychometrics.index', $type->id)" icon="eye">
                View Questions
            </flux:navmenu.item>
            <flux:navmenu.separator />

            <flux:navmenu.item wire:click="delete({{ $type->id }})" icon="trash" class="text-red-600"
                wire:confirm.prompt="Are you sure?\nType DELETE to confirm|DELETE">
                Delete
            </flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

    <!-- Modal for this specific row -->
    @if($type && $type->id)
    <flux:modal name="edit-test-type-{{ $type->id }}" variant="flyout" class="w-full max-w-2xl" wire:ignore x-data
        x-on:keydown.escape.window="$dispatch('close-modal')">
        <div wire:key="edit-test-type-modal-{{ $type->id }}">
            <livewire:admin.psychometrics.testtype.edit-test-type-modal :testTypeId="$type->id"
                :key="'edit-test-type-content-'.$type->id" />
        </div>
    </flux:modal>
    @endif
</div>