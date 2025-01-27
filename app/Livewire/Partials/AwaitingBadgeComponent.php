<?php

namespace App\Livewire\Partials;

use Livewire\Component;

class AwaitingBadgeComponent extends Component
{
    public string $size;

    public string $color;

    public string $text;

    public function mount(string $size = 'sm', string $color = 'sky', string $text = 'Awaiting')
    {
        $this->size = $size;
        $this->color = $color;
        $this->text = $text;
    }
}
