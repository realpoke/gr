<div class="flex justify-between items-center">
    <div>
        <flux:heading class="!mb-0">{{ $game->map->name }}</flux:heading>
        <flux:subheading>{{ $game->type->prettyName() }} - {{ $this->playedAt()->diffForHumans() }}</flux:subheading>
    </div>

    <flux:dropdown>
        <flux:button variant="ghost" squre icon="ellipsis-horizontal" inset="top bottom" />

        <flux:menu>
            <flux:menu.item wire:click="claim" icon="monitor-check">{{ __('navigation.claim-computer') }}</flux:menu.item>
            <flux:menu.item wire:navigate href="{{ $game->page() }}" icon="swords">{{ __('claim.found.game-details') }}</flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item wire:click="discard" icon="trash" variant="danger">{{ __('claim.found.discard') }}</flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
