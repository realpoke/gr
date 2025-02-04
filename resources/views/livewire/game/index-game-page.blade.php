@use('App\Enums\Game\GameStatusEnum')
@use('App\Enums\Game\GameTypeEnum')
@use('App\Enums\Rank\RankBracketEnum')
@use('App\Models\Map')

<div class="space-y-6">
    <div class="flex gap-4 sm:flex-row flex-col sm:items-center items-start">
        <flux:input.group class="sm:max-w-80">
            <flux:input :clearable="!empty($this->search)" wire:model.live.debounce="search" icon="magnifying-glass" placeholder="{{ __('label.search') }}" />

            <flux:select wire:model.live="amount" class="max-w-fit" variant="listbox">
                <flux:option value="15">15</flux:option>
                <flux:option value="25">25</flux:option>
                <flux:option value="50">50</flux:option>
            </flux:select>
        </flux:input.group>

        <flux:input.group class="!w-fit">
            <flux:tooltip content="{{ __('tooltip.filters') }}">
                <flux:button x-on:click="$flux.modal('game-filter').show()" square icon="adjustments-horizontal" />
            </flux:tooltip>

            @if($this->hasFiltersApplied)
                <flux:tooltip content="{{ __('tooltip.reset-filters') }}">
                    <flux:button square icon-trailing="x-mark" wire:click="resetFilters" />
                </flux:tooltip>
            @endif
        </flux:input.group>

        <flux:switch wire:model.live="live" label="Live" />
    </div>

    <flux:modal name="game-filter" variant="flyout" position="right">
        <form class="space-y-6" wire:submit="applyFilters">
            <div>
                <flux:heading>{{ __('filter.game.heading') }}</flux:heading>
                <flux:subheading>{{ __('filter.game.subheading') }}</flux:subheading>
            </div>

            <flux:field>
                <flux:label>{{ __('label.map') }}</flux:label>
                <flux:select :clearable="!empty($this->map)" wire:model="map" variant="listbox" searchable placeholder="{{ __('label.placeholder.map') }}">
                    @foreach (Map::all() as $map)
                        <flux:option value="{{ $map->id }}">{{ $map->name }}</flux:option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('label.bracket') }}</flux:label>
                <flux:select variant="listbox" wire:model="bracket">
                    <flux:option value="all">{{ __('option.all-rank-brackets') }}</flux:option>
                    @foreach (RankBracketEnum::cases() as $rankBracketEnum)
                        <flux:option value="{{ $rankBracketEnum }}">{{ $rankBracketEnum->prettyName() }}</flux:option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('label.game-type') }}</flux:label>
                <flux:select variant="listbox" wire:model="type">
                    <flux:option value="all">{{ __('option.all-types') }}</flux:option>
                    @foreach (GameTypeEnum::getValidGameTypes() as $gameTypeEnum)
                        <flux:option value="{{ $gameTypeEnum }}">{{ $gameTypeEnum->prettyName() }}</flux:option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:checkbox.group label="{{ __('label.status') }}" wire:model="statuses">
                    <flux:checkbox.all label="{{ __('option.every-status') }}" />
                    @foreach (GameStatusEnum::cases() as $gameStatusEnum)
                        <flux:checkbox value="{{ $gameStatusEnum }}" label="{{ $gameStatusEnum->prettyName() }}"/>
                    @endforeach
                </flux:checkbox.group>
            </flux:field>

            <flux:text wire:dirty>{{ __('label.filters-not-applied') }}</flux:text>

            <div class="flex md:flex-row flex-col gap-4">
                <flux:button class="w-full" type="submit" variant="primary">{{ __('filter.apply') }}</flux:button>
                <flux:button class="w-full" :disabled="!$this->hasFiltersApplied" wire:click="resetFilters">{{ __('filter.reset') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:separator text="{{ __('navigation.games') }}" />

    @if (!$this->live)
        <livewire:game.latest-game-component />
    @else
        <div class="flex items-start justify-center p-2">
            <livewire:partials.awaiting-badge-component text="Live Games" />
        </div>
    @endif

    <flux:card class="relative" wire:loading.delay.class="opacity-50 animate-pulse">
        <flux:icon.loading wire:loading.delay class="absolute top-2 left-2" variant="mini" />
        <flux:table :paginate="$this->games">
            <flux:columns>
                <flux:column>{{ __('column.map') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'elo_average'" :direction="$sortDirection" wire:click="sort('elo_average')">{{ __('column.average-elo') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'type'" direction="$sortDirection" wire:click="sort('type')">{{ __('column.type') }}</flux:column>
                <flux:column>{{ __('column.winners') }}</flux:column>
                <flux:column>{{ __('column.players') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('column.status') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'length'" :direction="$sortDirection" wire:click="sort('length')">{{ __('column.game-length') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('column.played-at') }}</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse($this->games as $game)
                    <livewire:game.game-row-component :game="$game" :key="$game->id" />
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
