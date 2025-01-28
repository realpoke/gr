<?php

namespace App\Actions\Claim;

use App\Actions\BaseAction;
use App\Events\PrivateClaimingEvent;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class DiscardFoundClaimGameAction extends BaseAction
{
    public function __construct(private Game $game) {}

    public function execute(): self
    {
        if (! Auth::check()) {
            return $this->setFailed('User not logged in.');
        }

        $claim = Auth::user()->claim;

        $claim->game_ids = collect($claim->game_ids)
            ->reject(fn ($gameId) => $gameId == $this->game->id)
            ->values()
            ->all();

        $claim->save();

        broadcast(new PrivateClaimingEvent);

        return $this->setSuccessful();
    }
}
