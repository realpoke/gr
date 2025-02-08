<flux:tooltip content="{{ $tooltip }}" position="bottom">
    <div class="relative">
        <div class="absolute left-1 top-1 max-h-6 max-w-6">
            @if (!is_null($this->src->get('team')))
                <img src="{{ $this->src->get('team') }}" class="w-full h-full object-contain" />
            @else
                <flux:icon.question-mark-circle />
            @endif
        </div>
        <div class="aspect-square">
            <img src="{{ $this->src->get('icon') }}" class="w-full h-full object-cover rounded" />
        </div>
    </div>
</flux:tooltip>
