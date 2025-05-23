@use('App\Enums\FactionEnum')
@use('App\Enums\SideEnum')
@use('App\Enums\Rank\RankBracketEnum')

<div class="flex flex-col gap-6">
    <!-- Info Block (Always on top) -->
    <div>
        <flux:heading>
            {{ __('label.game-info', ['map' => $this->map->name, 'played-at' => $this->game->created_at->diffForHumans(['short' => true]), 'elo-average' => $this->game->elo_average, 'bracket' => $this->game->bracket->prettyName(false), 'interval' => $this->interval->cascade()->forHumans(['short' => true])]) }}
        </flux:heading>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-flow-col xl:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]">
        <!-- Left Card -->
        <div>
            <flux:card class="space-y-6">
                <div class="flex gap-2 items-start">
                    <div class="relative">
                        <flux:avatar src="{{ $this->players->first()['profile_url'] ?? RankBracketEnum::UNRANKED->profileUrl() }}" />
                        @if ($this->players->first()['win'])
                            <flux:icon.crown class="text-amber-500 dark:text-amber-300 absolute -top-5 -left-2 -rotate-12" />
                        @endif
                        @if ($this->players->first()['mvp'])
                            <flux:icon.medal class="text-amber-500 dark:text-amber-300 absolute -bottom-4 left-0 right-0 mx-auto" />
                        @endif
                    </div>
                    <div>
                        <div class="flex gap-1 items-end">
                            <flux:heading size="lg">
                                {{ $this->players->first()['name'] }}
                            </flux:heading>
                            @if ($this->players->first()['stats'] ?? false)
                                <flux:text>
                                    {{ $this->players->first()['stats']['favorite_faction']->getSide()->prettyName() }}
                                </flux:text>
                            @endif
                        </div>
                        @if ($this->players->first()['stats'] ?? false)
                            <div class="flex gap-1 items-baseline">
                                <flux:text>{{ $this->players->first()['stats']['rank'] }}</flux:text>
                                <flux:text size="sm">{{ $this->players->first()['stats']['elo'] }} {{ $this->players->first()['stats']['bracket']->prettyName(false) }}</flux:text>
                                @if ($this->players->first()['elo_change'] > 0)
                                    <flux:text class="flex items-center">
                                        <span class="text-lime-500 dark:text-lime-300">
                                            {{ abs($this->players->first()['elo_change']) }}
                                        </span>
                                        <flux:icon.arrow-trending-up variant="mini" class="text-lime-500 dark:text-lime-300" />
                                    </flux:text>
                                @else
                                    <flux:text class="flex items-center">
                                        <span class="text-rose-500 dark:text-rose-300">
                                            {{ abs($this->players->first()['elo_change']) }}
                                        </span>
                                        <flux:icon.arrow-trending-down variant="mini" class="text-rose-500 dark:text-rose-300" />
                                    </flux:text>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <flux:separator text="{{ $this->players->first()['faction'] == '-1' ? FactionEnum::tryFrom($this->players->first()['faction'])->getSide()->prettyName().' ('.SideEnum::tryFrom($this->players->first()['side'])->prettyName().')' : FactionEnum::tryFrom($this->players->first()['faction'])->getSide()->prettyName() }}" />

                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.units', ['spent' => $this->players->first()['unitsCreated_spent'], 'amount' => $this->players->first()['unitsCreated_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->first()['unitsCreated'])->sortByDesc('TotalSpent') as $unit => $stat)
                                    <livewire:partials.zh-icon-component :unit="$unit" :tooltip="__('stat.unit', ['unit' => $unit, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-units') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.buildings', ['spent' => $this->players->first()['buildingsBuilt_spent'], 'amount' => $this->players->first()['buildingsBuilt_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->first()['buildingsBuilt'])->sortByDesc('TotalSpent') as $building => $stat)
                                    <livewire:partials.zh-icon-component :unit="$building" :tooltip="__('stat.building', ['building' => $building, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-buildings') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.upgrades', ['spent' => $this->players->first()['upgradesBuilt_spent'], 'amount' => $this->players->first()['upgradesBuilt_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->first()['upgradesBuilt'])->sortByDesc('TotalSpent') as $upgrade => $stat)
                                    <livewire:partials.zh-icon-component :unit="$upgrade" :tooltip="__('stat.upgrade', ['upgrade' => $upgrade, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-upgrades') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.powers', ['amount' => $this->players->first()['powersUsed_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->first()['powersUsed']) as $power => $count)
                                    <livewire:partials.zh-icon-component :unit="$power" :tooltip="__('stat.power', ['power' => $power, 'amount' => $stat['Count']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-powers') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>

                <flux:text>{{ __('stat.money-spent', ['spent' => $this->players->first()['moneySpent']]) }}</flux:text>
            </flux:card>
        </div>

        <!-- Middle Div -->
        <div class="flex flex-col items-center justify-start gap-4">
            <flux:badge :color="$this->game->status->getStatusBadgeColor()">
                {{ $this->game->status->prettyName() }}
            </flux:badge>
            <flux:heading size="xl">{{ __('label.versus') }}</flux:heading>
        </div>

        <!-- Right Card -->
        <div>
            <flux:card class="space-y-6">
                <div class="flex gap-2 items-start">
                    <div class="relative">
                        <flux:avatar src="{{ $this->players->last()['profile_url'] ?? RankBracketEnum::UNRANKED->profileUrl() }}" />
                        @if ($this->players->last()['win'])
                            <flux:icon.crown class="text-amber-500 dark:text-amber-300 absolute -top-5 -left-2 -rotate-12" />
                        @endif
                        @if ($this->players->last()['mvp'])
                            <flux:icon.medal class="text-amber-500 dark:text-amber-300 absolute -bottom-4 left-0 right-0 mx-auto" />
                        @endif
                    </div>
                    <div>
                        <div class="flex gap-1 items-end">
                            <flux:heading size="lg">
                                {{ $this->players->last()['name'] }}
                            </flux:heading>
                            @if ($this->players->last()['stats'] ?? false)
                                <flux:text>
                                    {{ $this->players->last()['stats']['favorite_faction']->getSide()->prettyName() }}
                                </flux:text>
                            @endif
                        </div>
                        @if ($this->players->last()['stats'] ?? false)
                            <div class="flex gap-1 items-baseline">
                                <flux:text>{{ $this->players->last()['stats']['rank'] }}</flux:text>
                                <flux:text size="sm">{{ $this->players->last()['stats']['elo'] }} {{ $this->players->last()['stats']['bracket']->prettyName(false) }}</flux:text>
                                @if ($this->players->last()['elo_change'] > 0)
                                    <flux:text class="flex items-center">
                                        <span class="text-lime-500 dark:text-lime-300">
                                            {{ abs($this->players->last()['elo_change']) }}
                                        </span>
                                        <flux:icon.arrow-trending-up variant="mini" class="text-lime-500 dark:text-lime-300" />
                                    </flux:text>
                                @else
                                    <flux:text class="flex items-center">
                                        <span class="text-rose-500 dark:text-rose-300">
                                            {{ abs($this->players->last()['elo_change']) }}
                                        </span>
                                        <flux:icon.arrow-trending-down variant="mini" class="text-rose-500 dark:text-rose-300" />
                                    </flux:text>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <flux:separator text="{{ $this->players->last()['faction'] == '-1' ? FactionEnum::tryFrom($this->players->last()['faction'])->getSide()->prettyName().' ('.SideEnum::tryFrom($this->players->last()['side'])->prettyName().')' : FactionEnum::tryFrom($this->players->last()['faction'])->getSide()->prettyName() }}" />

                <flux:accordion transition>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.units', ['spent' => $this->players->last()['unitsCreated_spent'], 'amount' => $this->players->last()['unitsCreated_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->last()['unitsCreated'])->sortByDesc('TotalSpent') as $unit => $stat)
                                    <livewire:partials.zh-icon-component :unit="$unit" :tooltip="__('stat.unit', ['unit' => $unit, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-units') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.buildings', ['spent' => $this->players->last()['buildingsBuilt_spent'], 'amount' => $this->players->last()['buildingsBuilt_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->last()['buildingsBuilt'])->sortByDesc('TotalSpent') as $building => $stat)
                                    <livewire:partials.zh-icon-component :unit="$building" :tooltip="__('stat.building', ['building' => $building, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-buildings') }}</flux:text>
                                    </div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.upgrades', ['spent' => $this->players->last()['upgradesBuilt_spent'], 'amount' => $this->players->last()['upgradesBuilt_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->last()['upgradesBuilt'])->sortByDesc('TotalSpent') as $upgrade => $stat)
                                    <livewire:partials.zh-icon-component :unit="$upgrade" :tooltip="__('stat.upgrade', ['upgrade' => $upgrade, 'amount' => $stat['Count'], 'spent' => $stat['TotalSpent']])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-upgrades') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                    <flux:accordion.item>
                        <flux:accordion.heading>{{ __('stat.powers', ['amount' => $this->players->last()['powersUsed_amount']]) }}</flux:accordion.heading>
                        <flux:accordion.content>
                            <div class="grid xl:grid-cols-6 md:grid-cols-8 grid-cols-5 gap-2">
                                @forelse(collect($this->players->last()['powersUsed']) as $power => $count)
                                    <livewire:partials.zh-icon-component :unit="$power" :tooltip="__('stat.power', ['power' => $power, 'amount' => $stat['Count'], ])" />
                                @empty
                                    </div>
                                        <flux:text>{{ __('stat.no-powers') }}</flux:text>
                                    <div>
                                @endforelse
                            </div>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>

                <flux:text>{{ __('stat.money-spent', ['spent' => $this->players->last()['moneySpent']]) }}</flux:text>
            </flux:card>
        </div>
    </div>
</div>
