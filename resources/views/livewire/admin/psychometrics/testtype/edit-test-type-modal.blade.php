<!-- resources/views/livewire/admin/psychometrics/testtype/edit-test-type-modal.blade.php -->
<div class="space-y-6">
    <div>
        <flux:heading size="lg">Edit Test Type</flux:heading>
        <flux:text class="mt-2">Update the test type details below.</flux:text>
    </div>

    <div class="space-y-4">
        <flux:input wire:model="name" label="Name" placeholder="Test type name" required />
        @error('name') <flux:text color="danger" size="sm">{{ $message }}</flux:text> @enderror

        <flux:textarea wire:model="description" label="Description" placeholder="Test type description" rows="3" />

        <flux:checkbox wire:model="is_active" label="Active" />
    </div>

    <div class="flex">
        <flux:spacer />
        <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
            <span wire:loading.remove>Save Changes</span>
            <span wire:loading>Saving...</span>
        </flux:button>
    </div>
</div>