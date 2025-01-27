<div style="all: unset; display: contents">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png" name="{{ config('app.name') }}" class="dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="{{ config('app.name') }}" class="hidden dark:flex" />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item wire:navigate icon="list-bullet" href="{{ route('index.leaderboard.page') }}">{{ __('navigation.leaderboard') }}</flux:navbar.item>
            <flux:navbar.item wire:navigate icon="swords" href="{{ route('index.game.page') }}">{{ __('navigation.games') }}</flux:navbar.item>
            <flux:navbar.item wire:navigate icon="globe-alt" href="{{ route('index.map.page') }}">{{ __('navigation.maps') }}</flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <div class="lg:block hidden">
            <livewire:partials.language-select-component class="hidden" />
            <livewire:partials.dark-mode-picker-component class="hidden" />
        </div>

        <flux:separator vertical class="mx-2 my-4 lg:block hidden" />
        @auth
            <flux:button
                class="lg:hidden block"
                variant="ghost"
                icon-trailing="bars-3"
                x-on:click="document.body.hasAttribute('data-show-stashed-sidebar') ? document.body.removeAttribute('data-show-stashed-sidebar') : document.body.setAttribute('data-show-stashed-sidebar', '')"
                data-flux-sidebar-toggle
                aria-label="{{ __('Toggle sidebar') }}">
                {{ $this->user->username }}
            </flux:button>
            <flux:dropdown position="top" align="start" class="lg:block hidden">
                <flux:button variant="ghost" icon-trailing="chevron-down">{{ $this->user->username }}</flux:button>

                <flux:menu>
                    <flux:menu.item x-on:click="$flux.modal('claim-computer-modal').show()" icon="monitor-check">{{ __('navigation.claim-computer') }}</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:navigate href="{{ route('setting.page') }}" icon="cog-6-tooth">{{ __('navigation.setting') }}</flux:menu.item>
                    <flux:menu.item wire:click="logout" icon="arrow-right-start-on-rectangle">{{ __('navigation.logout') }}</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        @else
            <flux:navbar class="lg:flex hidden">
                <flux:navbar.item wire:navigate icon="arrow-right-end-on-rectangle" href="{{ route('authenticate.page') }}">{{ __('navigation.login') }}</flux:navbar.item>
                <flux:navbar.item wire:navigate icon="finger-print" href="{{ route('register.page') }}">{{ __('navigation.register') }}</flux:navbar.item>
            </flux:navbar>
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="right" />
        @endauth
    </flux:header>

    @auth
        <livewire:claim.claim-modal-component />
    @endauth

    <flux:sidebar stashable sticky class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <div class="flex gap-2">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:separator vertical class="my-2" />

            <livewire:partials.language-select-component />
            <livewire:partials.dark-mode-picker-component />
        </div>

        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/logo.png" name="{{ config('app.name') }}" class="px-2 dark:hidden" />
        <flux:brand wire:navigate href="{{ route('landing.page') }}" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="{{ config('app.name') }}" class="px-2 hidden dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item wire:navigate icon="list-bullet" href="{{ route('index.leaderboard.page') }}">{{ __('navigation.leaderboard') }}</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="swords" href="{{ route('index.game.page') }}">{{ __('navigation.games') }}</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="globe-alt" href="{{ route('index.map.page') }}">{{ __('navigation.maps') }}</flux:navlist.item>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            @auth
                <flux:navlist.item x-on:click="$flux.modal('claim-computer-modal').show()" icon="monitor-check">{{ __('navigation.claim-computer') }}</flux:navlist.item>
                <flux:navlist.item wire:navigate href="{{ route('setting.page') }}" icon="cog-6-tooth">{{ __('navigation.setting') }}</flux:navlist.item>
                <flux:navlist.item wire:click="logout" icon="arrow-right-start-on-rectangle">{{ __('navigation.logout') }}</flux:navlist.item>
            @else
                <flux:navlist.item wire:navigate icon="arrow-right-end-on-rectangle" href="{{ route('authenticate.page') }}">{{ __('navigation.login') }}</flux:navlist.item>
                <flux:navlist.item wire:navigate icon="finger-print" href="{{ route('register.page') }}">{{ __('navigation.register') }}</flux:navlist.item>
            @endauth
        </flux:navlist>
    </flux:sidebar>
</div>
