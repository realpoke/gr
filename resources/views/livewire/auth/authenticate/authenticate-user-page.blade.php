<div
x-data="{
    twoFactorCode: $wire.entangle('form.twoFactorCode').live
}">
    <div class="flex justify-center">
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png"
            name="{{ config('app.name') }}" class="dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
            name="{{ config('app.name') }}" class="hidden dark:flex" />
    </div>
    <flux:heading size="xl" align="center">{{ __('auth.authenticate.title') }}</flux:heading>

    <div class="mt-10 space-y-6 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <flux:card>
            <form class="space-y-6" wire:submit="authenticate">
                <flux:input wire:model="form.email" label="{{ __('label.email') }}" />
                <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />


                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="form.remember" label="{{ __('label.remember-me') }}" />
                    <flux:link wire:navigate href="{{ route('resetpassword.page') }}">{{ __('navigation.forgot-password') }}</flux:link>
                </div>

                <flux:button variant="primary" type="submit" class="w-full">{{ __('navigation.login') }}</flux:button>
            </form>
        </flux:card>

        <flux:separator />

        <flux:heading align="center">
            {{ __('auth.not-a-member') }}
            <flux:link wire:navigate href="{{ route('register.page') }}">{{ __('navigation.register') }}</flux:link>
        </flux:heading>

        <flux:modal x-on:close="twoFactorCode = null" name="two-factor-modal" class="md:w-96 space-y-6">
            <div>
                <flux:heading size="lg">{{ __('auth.authenticate.modal.heading') }}</flux:heading>
                <flux:subheading>{{ __('auth.authenticate.modal.subheading') }}</flux:subheading>
            </div>

            <form wire:submit="authenticate" class="space-y-6">
                <flux:input wire:model="form.twoFactorCode" label="{{ __('label.two-factor-code') }}" placeholder="######" autocomplete="off" />

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">{{ __('navigation.login') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</div>
