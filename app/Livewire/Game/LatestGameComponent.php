<?php

namespace App\Livewire\Game;

use App\Models\Game;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy()]
#[On('echo:Public.Game,PublicGameCreatedEvent')]
class LatestGameComponent extends Component
{
    public $game;

    public function boot()
    {
        $this->game = Game::latest()->first();
    }

    public function placeholder()
    {
        return view('livewire.game.latest-game-placeholder');
    }
}
