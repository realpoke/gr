<?php

namespace App\Livewire\Game;

use Livewire\Attributes\On;
use Livewire\Component;

class GameRowComponent extends Component
{
    public $game;

    #[On('echo:Public.Game,PublicGameStatusUpdatedEvent')]
    public function refreshGame(array $data)
    {
        if ($this->game->id == $data['gameId']) {
            $this->game = $this->game->refresh();
        } else {
            $this->skipRender();
        }
    }
}
