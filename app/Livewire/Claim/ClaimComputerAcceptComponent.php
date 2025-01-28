<?php

namespace App\Livewire\Claim;

use App\Actions\Claim\ClaimFoundClaimGameAction;
use App\Actions\Claim\DiscardFoundClaimGameAction;
use App\Models\Game;
use Flux\Flux;
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
        $claimer = new ClaimFoundClaimGameAction($this->game);
        $claimer->handle();
    }

    public function discard()
    {
        $discarder = new DiscardFoundClaimGameAction($this->game);
        $discarder->handle();

        if ($discarder->successful()) {
            Flux::toast(__('toast.claim-discarded'));
        }
    }

    #[Computed()]
    public function playedAt(): Carbon
    {
        return $this->game->created_at;
    }
}
