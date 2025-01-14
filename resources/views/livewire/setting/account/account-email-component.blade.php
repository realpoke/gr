<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
        <flux:heading>{{ __('setting.account.email.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.account.email.subheading') }}</flux:subheading>
    </div>
    <div class="md:col-span-2">
        <form wire:submit="updateEmail" class="space-y-6">
            <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />
            <flux:field>
                <flux:label badge="{{ $this->hasVerifiedEmail ? __('label.verified') : __('label.unverified') }}">{{ __('label.email') }}</flux:label>

                <flux:input viewable wire:model="form.email" type="password" />
                <flux:error name="form.email" />
            </flux:field>

            <div class="flex gap-2">
                <flux:button type="submit">{{ __('navigation.save') }}</flux:button>
                @if (!$this->hasVerifiedEmail)
                    <flux:modal.trigger name="modal.email-verification-needed">
                        <flux:button>{{ __('setting.account.email.action') }}</flux:button>
                    </flux:modal.trigger>
                @endif
                <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
            </div>
        </form>

        <flux:modal name="modal.email-verification-needed" class="space-y-6 md:w-96">
            <div>
                <flux:heading size="lg">
                    {{ __('setting.account.email.modal-heading') }}
                    <flux:badge inset="top bottom" size="sm">{{ __('label.unverified') }}</flux:badge>
                </flux:heading>
                <flux:subheading>{{ __('setting.account.email.modal-subheading') }}</flux:subheading>
                <flux:error name="slow" />
            </div>

            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('navigation.later') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" class="ml-4" wire:click="sendEmailVerification">
                    {{ __('setting.account.email.modal-action') }}
                </flux:button>
            </div>
        </flux:modal>
    </div>
</div>
