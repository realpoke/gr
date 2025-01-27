<div>
    <div class="flex justify-center">
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png"
            name="{{ config('app.name') }}" class="dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
            name="{{ config('app.name') }}" class="hidden dark:flex" />
    </div>
    <flux:heading size="xl" align="center">{{ __('auth.register.title') }}</flux:heading>

    <div class="mt-10 space-y-6 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <flux:card>
            <form class="space-y-6" wire:submit="register">
                <flux:input wire:model="form.email" type="email" label="{{ __('label.email') }}" />
                <flux:input wire:model="form.username" label="{{ __('label.username') }}" />
                <flux:input wire:model="form.password" type="password" label="{{ __('label.password') }}" />
                <flux:input wire:model="form.passwordConfirmation" type="password" label="{{ __('label.confirm-password') }}" />

                <div class="flex md:flex-row md:items-center gap-y-1 justify-between flex-col">
                    <flux:checkbox wire:model="form.terms" label="{{ __('label.agree-terms-and-conditions') }}" />
                    <flux:link class="flex items-center gap-1" external href="{{ route('document.page', ['document' => 'terms']) }}">
                        {{ __('navigation.terms') }}
                        <flux:icon.arrow-top-right-on-square variant="micro" />
                    </flux:link>
                </div>

                <flux:button variant="primary" type="submit" class="w-full">{{ __('auth.register.action') }}</flux:button>
            </form>
        </flux:card>

        <flux:separator />

        <flux:heading align="center">
            {{ __('auth.already-a-member') }}
            <flux:link wire:navigate href="{{ route('authenticate.page') }}">{{ __('navigation.login') }}</flux:link>
        </flux:heading>
    </div>
</div>
