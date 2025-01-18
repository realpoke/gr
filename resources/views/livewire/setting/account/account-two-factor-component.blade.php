<div class="grid max-w-7xl grid-cols-1 gap-6 py-6 md:grid-cols-3"
x-data="{
    qrSvg: $wire.entangle('qrSvg').live,
    twoFactorCode: $wire.entangle('form.twoFactorCode').live,
    password: $wire.entangle('form.password').live,
    twoFactorSecret: $wire.entangle('twoFactorSecret').live,
    qrMode: true
}">
    <div>
        <flux:heading>{{ __('setting.account.two-factor.heading') }}</flux:heading>
        <flux:subheading>{{ __('setting.account.two-factor.subheading') }}</flux:subheading>
    </div>
    <div class="md:col-span-2">
        @if ($this->hasTwoFactor)
            <form wire:submit="disableTwoFactor" class="space-y-6">
                <flux:heading>{{ __('setting.account.two-factor.heading-disable') }}</flux:heading>
                <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />
                <flux:input wire:model="form.twoFactorCode" label="{{ __('label.two-factor-code') }}" placeholder="######" autocomplete="off" />

                <div class="flex gap-2">
                    <flux:button type="submit">{{ __('setting.account.two-factor.disable') }}</flux:button>
                    <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
                </div>
            </form>
        @else
            <form wire:submit="enableTwoFactor" class="space-y-6">
                <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />

                <div class="flex gap-2">
                    <flux:button type="submit">{{ __('setting.account.two-factor.enable') }}</flux:button>
                    <flux:button wire:dirty wire:target="form" wire:click="resetForm">{{ __('label.reset') }}</flux:button>
                </div>
            </form>

            <flux:modal x-on:close="qrMode = true; password = ''; qrSvg = ''; twoFactorCode = ''; twoFactorSecret = ''" name="two-factor-modal" class="space-y-6 md:max-w-96">
                <div>
                    <flux:heading size="lg">{{ __('setting.account.two-factor.modal.heading') }}</flux:heading>
                    <flux:subheading x-show="qrMode">{{ __('setting.account.two-factor.modal.qr-subheading') }}</flux:subheading>
                    <flux:subheading x-show="!qrMode">{{ __('setting.account.two-factor.modal.key-subheading') }}</flux:subheading>
                </div>

                <div x-show="!qrMode">
                    <flux:input icon="key" wire:model="twoFactorSecret" label="{{ __('label.setup-key') }}" readonly copyable />
                </div>

                <div x-show="qrMode">
                    <flux:heading>{{ __('label.qr-code') }}</flux:heading>
                    <div class="flex justify-center mt-2">
                        {!! $this->qrSvg !!}
                    </div>
                </div>

                <flux:separator />

                <form wire:submit="verifyTwoFactor" class="space-y-6">
                    <div>
                        <flux:subheading class="mb-2">{{ __('setting.account.two-factor.modal.subheading') }}</flux:subheading>
                        <flux:input wire:model="form.twoFactorCode" label="{{ __('label.two-factor-code') }}" placeholder="######" autocomplete="off" />
                    </div>

                    <flux:checkbox wire:model="form.logoutOther" label="{{ __('label.logout-other') }}" description="{{ __('label.two-factor-logout-other-description') }}" />
                    <div class="flex gap-4">
                        <flux:dropdown position="top" align="start">
                            <flux:button icon="ellipsis-horizontal" variant="subtle" square></flux:button>

                            <flux:menu>
                                <flux:menu.item x-on:click="qrMode = !qrMode" x-show="qrMode">{{ __('setting.account.two-factor.modal.use-key') }}</flux:menu.item>
                                <flux:menu.item x-on:click="qrMode = !qrMode" x-show="!qrMode">{{ __('setting.account.two-factor.modal.use-qr') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>

                        <flux:spacer />

                        <flux:button type="submit" variant="primary">{{ __('setting.account.two-factor.modal.action') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        @endif
    </div>
</div>
