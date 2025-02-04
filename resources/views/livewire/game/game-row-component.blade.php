@use('Carbon\CarbonInterval')
@use('App\Enums\FactionEnum')

<flux:row>
    <flux:cell>{{ $this->game->map->name }}</flux:cell>
    <flux:cell>
        @if ($this->game->elo_average)
            {{ $this->game->elo_average }}
            {{ $this->game->bracket->prettyName() }}
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell>{{ $this->game->type->prettyName() }}</flux:cell>
    <flux:cell>
        @if ($this->game->winners->count() > 0)
            <div class="flex -space-x-3">
                <div class="flex -space-x-2 overflow-hidden">
                    @foreach ($game->winners as $winner)
                        <flux:tooltip content="{{ $winner->username }}">
                            <flux:avatar class="border-2 dark:border-zinc-700 border-white bg-white" size="xs" src="{{ $winner->avatar }}" />
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
                    <flux:tooltip content="{{ $player['name'] }} as {{ FactionEnum::tryFrom($player['faction'])->getSide()->prettyName() }}">
                        <flux:avatar class="border-x-2 dark:border-zinc-700 border-white bg-white" size="xs"/>
                    </flux:tooltip>
                @endforeach
            </div>
        @else
            <flux:icon.minus variant="mini" inset="top bottom" />
        @endif
    </flux:cell>
    <flux:cell><flux:badge :color="$game->status->getStatusBadgeColor()" size="sm" inset="top bottom">{{ ucfirst($game->status->value) ?? '...' }}</flux:badge></flux:cell>
    <flux:cell>{{ CarbonInterval::seconds($this->game->data['metaData']['gameInterval'])->cascade()->forHumans(['short' => true]) }}</flux:cell>
    <flux:cell>{{ $this->game->created_at->diffForHumans(['short' => true]) }}</flux:cell>
</flux:row>
