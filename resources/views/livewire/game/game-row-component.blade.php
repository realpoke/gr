@use('Carbon\CarbonInterval')
@use('App\Enums\FactionEnum')
@use('App\Enums\Rank\RankBracketEnum')

<flux:row>
    <flux:cell>{{ $this->game->map->name }}</flux:cell>
    <flux:cell class="!py-0">
        @if ($this->game->elo_average)
            <div class="flex items-start flex-col">
                <flux:text>{{ $this->game->elo_average }}</flux:text>
                <flux:text size="sm">{{ $this->game->bracket->prettyName(withRange: false) }}</flux:text>
            </div>
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell>{{ $this->game->type->prettyName() }}</flux:cell>
    <flux:cell class="!py-0">
        @if ($this->game->winners->count() > 0)
            <div class="flex -space-x-3">
                <div class="flex -space-x-2 overflow-hidden">
                    @foreach ($game->winners as $winner)
                        <flux:tooltip content="{{ $winner->username }}">
                            <flux:avatar class="border-2 dark:border-zinc-700 border-white" size="xs" src="{{ RankBracketEnum::UNRANKED->profileUrl() }}" />
                        </flux:tooltip>
                    @endforeach
                </div>
                <flux:icon.trophy variant="micro" class="pointer-events-none text-amber-500 dark:text-amber-300 animate-bounce" />
            </div>
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell class="!py-0">
        @if (count($this->game['data']['players']) > 0)
            <div class="flex -space-x-2 overflow-hidden">
                @foreach ($this->game['data']['players'] as $player)
                    <flux:tooltip content="{{ __('tooltip.game-player-as', ['name' => $player['name'], 'faction' => FactionEnum::tryFrom($player['faction'])->getSide()->prettyName()]) }}">
                        <flux:avatar src="{{ RankBracketEnum::UNRANKED->profileUrl()}}" class="border-2 dark:border-zinc-700 border-white" size="xs"/>
                    </flux:tooltip>
                @endforeach
            </div>
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell><flux:badge :color="$game->status->getStatusBadgeColor()" size="sm" inset="top bottom">{{ $game->status->prettyName() }}</flux:badge></flux:cell>
    <flux:cell>
        <flux:text>{{ CarbonInterval::seconds($this->game->data['metaData']['gameInterval'])->cascade()->forHumans(['short' => true]) }}</flux:text>
    </flux:cell>
    <flux:cell>
        <flux:tooltip position="left" content="{{ __('tooltip.game-created-at', ['time' => $this->game->created_at]) }}">
            <flux:text>{{ $this->game->created_at->diffForHumans(['short' => true]) }}</flux:text>
        </flux:tooltip>
    </flux:cell>
    <flux:cell>
        <flux:dropdown class="flex items-center">
            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

            <flux:menu>
                <flux:menu.item wire:navigate href="{{ $game->page() }}" icon="swords">{{ __('navigation.game-details') }}</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:cell>
</flux:row>
