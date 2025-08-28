<!-- resources/views/components/psychometrics/competence-actions.blade.php -->
<div class="flex gap-2">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="filled" icon="ellipsis-horizontal" />
        <flux:navmenu>
            {{-- <flux:navmenu.item :href="route('admin.psychometrics.tests.competencies.edit', [$test, $competence])"
                icon="pencil-square">
                Edit
            </flux:navmenu.item> --}}

            <flux:modal.trigger name="competency-modal">
                <flux:navmenu.item
                    wire:click="$dispatch('open-competency-modal', { mode: 'edit', competencyId: {{ $competence->id }} })"
                    icon="pencil-square">
                    Edit
                </flux:navmenu.item>
            </flux:modal.trigger>

            <flux:navmenu.item wire:click="detachCompetence({{ $test->id }}, {{ $competence->id }})" icon="trash"
                class="text-red-600" wire:confirm.prompt="Are you sure?\nType DELETE to confirm|DELETE">
                Remove
            </flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>
</div>