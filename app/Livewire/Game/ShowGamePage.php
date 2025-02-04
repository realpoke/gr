<?php

namespace App\Livewire\Game;

use App\Models\Game;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.games')]
class ShowGamePage extends Component
{
    public Game $game;

    public function render()
    {
        return view('livewire.game.type.game-type-'.$this->game->type->value.'-component');
    }
}
