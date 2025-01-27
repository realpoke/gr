<flux:badge variant="pill" :size="$this->size" :color="$this->color">
    <div class="-left-2 relative -mr-1">
        <flux:icon.circle class="absolute animate-pulse" variant="mini" inset="left top bottom" />
        <flux:icon.dot class="animate-ping" variant="mini" inset="left top bottom" />
    </div>
    {{ $this->text }}
</flux:badge>
