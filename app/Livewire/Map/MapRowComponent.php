<?php

namespace App\Livewire\Map;

use App\Models\Map;
use Livewire\Component;

class MapRowComponent extends Component
{
    public $map;

    public function mapVerified()
    {
        $this->map = Map::find($this->map->id);
    }
}
