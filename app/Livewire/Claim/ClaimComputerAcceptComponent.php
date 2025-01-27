<?php

namespace App\Livewire\Claim;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class ClaimComputerAcceptComponent extends Component
{
    #[Modelable]
    public Game $game;

    public function claim()
    {
        // TODO: Implement claim action with validation
    }

    public function discard()
    {
        // TODO: Implement discard action with validation
    }

    #[Computed()]
    public function playedAt(): Carbon
    {
        return $this->game->created_at;
    }
}
