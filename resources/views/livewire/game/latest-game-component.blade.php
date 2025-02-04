<flux:card class="relative !py-0">
    <flux:tooltip content="{{ __('tooltip.latest-game') }}">
        <flux:icon.question-mark-circle class="absolute left-0.5 top-0.5" variant="mini" />
    </flux:tooltip>
    <flux:table>
        <livewire:game.game-row-component :game="$this->game" key="{{ 'latest-game-'. $this->game->id }}" />
    </flux:table>
</flux:card>
