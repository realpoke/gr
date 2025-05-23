<?php

namespace App\Actions\Claim;

use App\Actions\BaseAction;
use App\Events\PrivateFoundClaimableComputerEvent;
use App\Models\Claim;
use App\Models\Game;
use App\Models\Gentool;

use function Illuminate\Support\defer;

class AddCanClaimGameAction extends BaseAction
{
    public function __construct(
        private string $replayUsername,
        private Gentool $gentool,
        private Game $game,
    ) {}

    public function execute(): self
    {
        $claim = Claim::where('name', $this->replayUsername)
            ->notExpired()
            ->first();

        if (! is_null($claim) && ! is_null($this->gentool)) {
            $claim->game_ids = array_unique(array_merge($claim->game_ids ?? [], [$this->game->id]));
            $claim->save();

            defer(fn () => broadcast(new PrivateFoundClaimableComputerEvent($claim->user_id)));
        }

        return $this->setSuccessful();
    }
}
