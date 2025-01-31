<div class="space-y-6">
    <div class="flex gap-4">
        <flux:input class="sm:max-w-80 w-full" :clearable="!empty($this->search)" wire:model.live.debounce="search" icon="magnifying-glass" placeholder="{{ __('label.search') }}" />

        <flux:input.group class="w-auto">
            <flux:tooltip content="{{ __('tooltip.filters') }}">
                <flux:button x-on:click="$flux.modal('game-filter').show()" square icon="adjustments-horizontal" />
            </flux:tooltip>

            @if($this->hasFiltersApplied)
                <flux:tooltip content="{{ __('tooltip.reset-filters') }}">
                    <flux:button square icon-trailing="x-mark" wire:click="resetFilters" />
                </flux:tooltip>
            @endif
        </flux:input.group>
    </div>

    <flux:modal name="game-filter" variant="flyout" position="right">
        <form class="space-y-6" wire:submit="applyFilters">
            <div>
                <flux:heading>{{ __('filter.game.heading') }}</flux:heading>
                <flux:subheading>{{ __('filter.game.subheading') }}</flux:subheading>
            </div>

            <flux:text wire:dirty>{{ __('label.filters-not-applied') }}</flux:text>

            <div class="flex md:flex-row flex-col gap-4">
                <flux:button class="w-full" type="submit" variant="primary">{{ __('filter.apply') }}</flux:button>
                <flux:button class="w-full" :disabled="!$this->hasFiltersApplied" wire:click="resetFilters">{{ __('filter.reset') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:separator text="{{ __('navigation.games') }}" />

    <flux:card class="relative" wire:loading.delay.class="opacity-50 animate-pulse">
        <flux:icon.loading wire:loading.delay class="absolute top-2 left-2" variant="mini" />
        <flux:table :paginate="$this->games">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'hash'" :direction="$sortDirection" wire:click="sort('hash')">{{ __('column.hash') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('column.updated-at') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('column.created-at') }}</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse($this->games as $game)
                    {{ $game->hash }}
                @empty
                    <flux:row>
                        <flux:cell colspan="4">
                            <flux:heading>{{ __('label.no-games') }}</flux:heading>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
    </flux:card>
</div>
