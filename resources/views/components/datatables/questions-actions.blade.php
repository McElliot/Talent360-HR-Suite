<div class="flex gap-2">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="filled" icon="ellipsis-horizontal" />
        <flux:navmenu>
            <flux:navmenu.item
                href="{{ route('admin.psychometrics.tests.questions.edit', ['test' => $question->psychometric_test_id, 'question' => $question->id]) }}"
                icon="pencil-square">
                Edit
            </flux:navmenu.item>
            {{-- <flux:modal.trigger name="test-modal">
                <flux:navmenu.item
                    wire:click="$dispatch('open-test-modal', { mode: 'edit', testId: {{ $question->id }} })"
                    icon="pencil-square">
                    Edit
                </flux:navmenu.item>
            </flux:modal.trigger> --}}

            <flux:navmenu.item wire:click="toggleRequired({{ $question->id }})"
                icon="{{ $question->is_required ? 'x-circle' : 'check-circle' }}"
                class="{{ $question->is_required ? '!text-yellow-600 hover:!text-yellow-700' : '!text-green-600 hover:!text-green-700' }}">
                {{ $question->is_required ? 'Mark as Not Required' : 'Mark as Required' }}
            </flux:navmenu.item>

            <flux:navmenu.item wire:click="toggleStatus({{ $question->id }})"
                icon="{{ $question->is_active ? 'x-circle' : 'check-circle' }}"
                class="{{ $question->is_active ? '!text-yellow-600 hover:!text-yellow-700' : '!text-green-600 hover:!text-green-700' }}">
                {{ $question->is_active ? 'Deactivate' : 'Activate' }}
            </flux:navmenu.item>

            <flux:navmenu.separator />

            <flux:navmenu.item wire:click="delete({{ $question->id }})" icon="trash" class="text-red-600"
                wire:confirm.prompt="Are you sure?\nType DELETE to confirm|DELETE">
                Delete
            </flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>
</div>