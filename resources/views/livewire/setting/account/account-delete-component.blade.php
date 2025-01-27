<div x-data="{ password: $wire.entangle('form.password') }" class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.account.delete.heading') }}</flux:heading>
        <flux:subheading>
            {{ __('setting.account.delete.subheading') }}
        </flux:subheading>
    </div>

    <div class="space-y-6 md:col-span-2">
        <flux:modal.trigger name="delete-profile">
            <flux:button variant="danger">{{ __('setting.account.delete.action') }}</flux:button>
        </flux:modal.trigger>

        <flux:modal x-on:close="password = ''" name="delete-profile" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('setting.account.delete.modal.heading') }}</flux:heading>

                <flux:subheading>
                    {{ __('setting.account.delete.modal.subheading') }}
                </flux:subheading>

            </div>

            <form wire:submit="deleteAccount" class="space-y-6">
                <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button  variant="ghost">{{ __('navigation.cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="danger">{{ __('setting.account.delete.modal.action') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</div>
