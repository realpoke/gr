<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.profile.username.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.profile.username.subheading') }}</flux:subheading>
    </div>
    <div class="md:col-span-2">
        <form wire:submit="updateUsername" class="space-y-6">
            <flux:input wire:model="form.username" label="{{ __('label.username') }}" />

            <div class="flex gap-2">
                <flux:button type="submit">{{ __('navigation.save') }}</flux:button>
                <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
            </div>
        </form>
    </div>
</div>
