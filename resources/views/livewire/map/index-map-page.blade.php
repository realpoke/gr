<div class="space-y-6">
    <div class="flex sm:flex-row flex-col gap-4">
        <flux:input.group class="sm:max-w-80 w-full">
            <flux:input :clearable="!empty($this->search)" wire:model.live.debounce="search" icon="magnifying-glass" placeholder="{{ __('label.search') }}" />

            <flux:select wire:model.live="amount" class="max-w-fit" variant="listbox">
                <flux:option value="15">15</flux:option>
                <flux:option value="25">25</flux:option>
                <flux:option value="50">50</flux:option>
            </flux:select>
        </flux:input.group>

        <flux:input.group class="w-auto">
            <flux:tooltip content="{{ __('tooltip.filters') }}">
                <flux:button x-on:click="$flux.modal('map-filter').show()" square icon="adjustments-horizontal" />
            </flux:tooltip>

            @if($this->hasFiltersApplied)
                <flux:tooltip content="{{ __('tooltip.reset-filters') }}">
                    <flux:button square icon-trailing="x-mark" wire:click="resetFilters" />
                </flux:tooltip>
            @endif
        </flux:input.group>
    </div>

    <flux:modal name="map-filter" variant="flyout" position="right">
        <form class="space-y-6" wire:submit="applyFilters">
            <div>
                <flux:heading>{{ __('filter.map.heading') }}</flux:heading>
                <flux:subheading>{{ __('filter.map.subheading') }}</flux:subheading>
            </div>

            <flux:checkbox label="{{ __('label.verified-only') }}" wire:model="verifiedOnly" />

            <flux:field>
                <flux:label>{{ __('label.game-mode') }}</flux:label>
                <flux:select variant="listbox" wire:model="mode">
                    <flux:option value="all">{{ __('option.all-game-modes') }}</flux:option>
                    @foreach (App\Enums\Game\GameModeEnum::cases() as $option)
                        <flux:option value="{{ $option->value }}">{{ $option->prettyName() }}</flux:option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:text wire:dirty>{{ __('label.filters-not-applied') }}</flux:text>

            <div class="flex md:flex-row flex-col gap-4">
                <flux:button class="w-full" type="submit" variant="primary">{{ __('filter.apply') }}</flux:button>
                <flux:button class="w-full" :disabled="!$this->hasFiltersApplied" wire:click="resetFilters">{{ __('filter.reset') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:separator text="{{ __('navigation.maps') }}" />

    <flux:card class="relative" wire:loading.delay.class="opacity-50 animate-pulse">
        <flux:icon.loading wire:loading.delay class="absolute top-2 left-2" variant="mini" />
        <flux:table :paginate="$this->maps">
            <flux:columns>
                <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('column.name') }}</flux:column>
                <flux:column>{{ __('column.game-type') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'plays'" :direction="$sortDirection" wire:click="sort('plays')">{{ __('column.play-count') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'plays_monthly'" :direction="$sortDirection" wire:click="sort('plays_monthly')">{{ __('column.monthly') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'plays_weekly'" :direction="$sortDirection" wire:click="sort('plays_weekly')">{{ __('column.weekly') }}</flux:column>
                <flux:column>{{ __('column.verified-at') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('column.last-played') }}</flux:column>
                <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('column.added-at') }}</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse($this->maps as $map)
                    <livewire:map.map-row-component :map="$map" :key="$map->id" />
                @empty
                    <flux:row>
                        <flux:cell colspan="4">
                            <flux:heading>{{ __('label.no-maps') }}</flux:heading>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
    </flux:card>
</div>
