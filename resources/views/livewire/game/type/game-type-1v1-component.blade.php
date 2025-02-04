<div class="grid grid-cols-1 gap-6 xl:grid-flow-col xl:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]">
    <!-- Left Card -->
    <flux:card class="xl:order-1 order-2">
        <flux:heading>{{ $this->players->first()['name'] }}</flux:heading>
        <flux:subheading>{{ collect($this->players->first()) }}</flux:subheading>
    </flux:card>

    <!-- Middle Div -->
    <div class="xl:order-2 order-1 flex flex-col items-center">
        <!--{{ $this->game->elo_average }}-->
        <!--{{ $this->game->bracket->prettyName() }}-->
        <!--{{ $this->played_at }}-->
        <!--{{ $this->map->name }}-->
        <!--{{ $this->map->verified_at }}-->
        <flux:badge :color="$this->game->status->getStatusBadgeColor()">
            {{ $this->game->status->prettyName() }}
        </flux:badge>
        <!--{{ $this->players }}-->
        <!--{{ $this->observers }}-->
    </div>

    <!-- Right Card -->
    <flux:card class="order-3">
        <flux:heading>{{ $this->players->last()['name'] }}</flux:heading>
    </flux:card>
</div>
