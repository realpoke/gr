<div>
    <div class="flex justify-center">
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png"
            name="{{ config('app.name') }}" class="dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
            name="{{ config('app.name') }}" class="hidden dark:flex" />
    </div>
    <flux:heading size="xl" align="center">
        {{ __('auth.password-verification-needed.title') }}
    </flux:heading>

    <div class="mt-10 space-y-6 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <flux:card class="space-y-6">
            <div>
                <flux:heading>{{ __('auth.password-verification-needed.heading') }}</flux:heading>
                <flux:error name="resend" />
                <flux:subheading>{{ __('auth.password-verification-needed.subheading') }}</flux:subheading>
            </div>

            <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />

            <div class="flex gap-2">
                <flux:spacer />

                <flux:button wire:click="logout" variant="ghost">{{ __('navigation.logout') }}</flux:button>
                <flux:button wire:click="checkPassword" variant="primary">{{ __('auth.password-verification-needed.action') }}</flux:button>
            </div>
        </flux:card>

        <flux:separator />

        <flux:heading align="center">
            {{ __('auth.change-account-information') }}
            <flux:link wire:navigate href="{{ route('setting.page') }}">{{ __('navigation.account-settings') }}</flux:link>
        </flux:heading>
    </div>
</div>
