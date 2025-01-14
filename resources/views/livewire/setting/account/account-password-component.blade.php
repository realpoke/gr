<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.account.password.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.account.password.subheading') }}</flux:subheading>
    </div>

    <div class="md:col-span-2">
        <form wire:submit="updatePassword" class="space-y-6">
            <flux:input wire:model="form.currentPassword" type="password" label="{{ __('label.current-password') }}" />
            <flux:input wire:model="form.password" type="password" label="{{ __('label.new-password') }}" />
            <flux:input wire:model="form.passwordConfirmation" type="password" label="{{ __('label.confirm-new-password') }}" />

            <flux:checkbox
                wire:model="form.logoutOther"
                label="{{ __('label.logout-other') }}"
                description="{{ __('label.new-password-logout-other-description') }}"
            />

            <div class="flex gap-2">
                <flux:button type="submit">{{ __('navigation.save') }}</flux:button>
                <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
            </div>
        </form>
    </div>
</div>
