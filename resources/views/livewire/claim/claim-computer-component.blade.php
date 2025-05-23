<div>
    @if (! $this->user->canClaimMoreComputers())
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('claim.full.heading') }} ({{ $this->user->claimCount() }}/{{ $this->maxClaims}})</flux:heading>
                <flux:subheading>{{ __('claim.full.subheading') }}</flux:subheading>
            </div>

            <div>
                <flux:button wire:navigate href="{{ route('setting.page') }}">{{ __('claim.full.action') }}</flux:button>
            </div>
        </div>
    @elseif ($this->isClaming)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('claim.start.heading') }} ({{ $this->user->claimCount() }}/{{ $this->maxClaims}})</flux:heading>
                <flux:subheading>{{ __('claim.search.subheading') }}</flux:subheading>
                <flux:text>{{ __('claim.search.text', ['time' => $this->expiresAt->diffForHumans(parts: 2)]) }}</flux:text>
            </div>

            <flux:input wire:model="claimName" label="Claim Name" readonly copyable />

            <flux:separator text="{{ __('claim.search.separator') }}" />

            @foreach($this->gamesFound as $game)
                <livewire:claim.claim-computer-accept-component :key="$game->hash" :game="$game" />
            @endforeach

            <div class="flex items-start justify-center">
                <livewire:partials.awaiting-badge-component text="Searching" />
            </div>
        </div>
    @else
        <form wire:submit="startClaiming" class="space-y-6"
            x-init="$watch('clear', value => {
                password = '';
                private = false;
            })"
            x-data="{
                password: $wire.entangle('form.password'),
                private: $wire.entangle('form.private'),
        }">
            <div>
                <flux:heading size="lg">{{ __('claim.start.heading') }} ({{ $this->user->claimCount() }}/{{ $this->maxClaims}})</flux:heading>
                <flux:subheading>{{ __('claim.start.subheading') }}</flux:subheading>
                <flux:text>{{ __('claim.start.text', ['time' => $this->within]) }}</flux:text>
            </div>

            <flux:input wire:model="form.password" label="Password" type="password" />

            <flux:checkbox wire:model="form.private" label="Is this your personal computer?" />

            <div>
                <flux:button type="submit">{{ __('claim.start.action') }}</flux:button>
            </div>
        </form>
    @endif
</div>
