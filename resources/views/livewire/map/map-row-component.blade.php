<flux:row x-data="{
    plays: {{ $this->map->plays }},
    playsMonthly: {{ $this->map->plays_monthly }},
    playsWeekly: {{ $this->map->plays_weekly }},
    showIcon: false,
    timeoutId: null,
    updatePlays(newPlays, newPlaysMonthly, newPlaysWeekly) {
        this.plays = newPlays;
        this.playsMonthly = newPlaysMonthly;
        this.playsWeekly = newPlaysWeekly;
        this.showIcon = false;
        this.showIcon = true;

        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        this.timeoutId = setTimeout(() => this.showIcon = false, 3000);
    }
}"
x-init="
    Echo.channel('Public.Map')
        .listen('PublicMapPlayedEvent', (data) => {
            if (data.mapId == {{ $this->map->id }}) {
                updatePlays(data.plays, data.playsMonthly, data.playsWeekly);
            }
        })
        .listen('PublicMapVerifiedEvent', (data) => {
            if (data.mapId == {{ $this->map->id }}) {
                $wire.mapVerified();
            }
        });
">
    <flux:cell>{{ $this->map->name }}</flux:cell>
    <flux:cell>
        @if (count($this->map->types) > 0)
            {{ implode(', ', collect($this->map->types)->map(fn ($type) => $type->prettyName())->toArray()) }}
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell>
        <div class="flex gap-1 items-center">
            <span
                :class="showIcon ? 'text-lime-500 dark:text-lime-300 scale-110' : 'scale-100'"
                class="transition-transform duration-300 ease-out"
                x-text="plays">{{ $this->map->plays }}
            </span>
            <flux:icon.chevron-double-up
                x-cloak
                x-show="showIcon"
                variant="mini"
                inset="top bottom"
                square
                x-transition:enter="transition ease-in duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-75"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-75"
                class="text-lime-500 dark:text-lime-300"
            />
        </div>
    </flux:cell>
    <flux:cell>
        <div class="flex gap-1 items-center">
            <span
                :class="showIcon ? 'text-lime-500 dark:text-lime-300 scale-110' : 'scale-100'"
                class="transition-transform duration-300 ease-out"
                x-text="playsMonthly">{{ $this->map->plays_monthly }}
            </span>
            <flux:icon.chevron-double-up
                x-cloak
                x-show="showIcon"
                variant="mini"
                inset="top bottom"
                square
                x-transition:enter="transition ease-in duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-75"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-75"
                class="text-lime-500 dark:text-lime-300"
            />
        </div>
    </flux:cell>
    <flux:cell>
        <div class="flex gap-1 items-center">
            <span
                :class="showIcon ? 'text-lime-500 dark:text-lime-300 scale-110' : 'scale-100'"
                class="transition-transform duration-300 ease-out"
                x-text="playsWeekly">{{ $this->map->plays_weekly }}
            </span>
            <flux:icon.chevron-double-up
                x-cloak
                x-show="showIcon"
                variant="mini"
                inset="top bottom"
                square
                x-transition:enter="transition ease-in duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-75"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-out duration-300"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-75"
                class="text-lime-500 dark:text-lime-300"
            />
        </div>
    </flux:cell>
    <flux:cell>
        @if ($this->map->verified_at)
            <flux:tooltip position="right" content="{{ __('tooltip.verified_at', ['date' => $this->map->verified_at]) }}">
                <flux:icon.check variant="mini" class="text-lime-500 dark:text-lime-300" inset="top bottom" />
            </flux:tooltip>
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell>{{ $this->map->updated_at->diffForHumans(['short' => true]) }}</flux:cell>
    <flux:cell>{{ $this->map->created_at->diffForHumans(['short' => true]) }}</flux:cell>
    <flux:cell>
        <flux:dropdown class="flex items-center">
            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

            <flux:menu>
                <flux:menu.item wire:navigate href="{{ route('index.game.page', ['map' => $this->map->id]) }}" icon="swords">{{ __('navigation.games-on-map') }}</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:cell>
</flux:row>
