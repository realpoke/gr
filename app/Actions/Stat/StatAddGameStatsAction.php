<?php

namespace App\Actions\Stat;

use App\Actions\BaseAction;
use App\Models\Game;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Support\Collection;

class StatAddGameStatsAction extends BaseAction
{
    public function __construct(
        private Stat $stat,
        private Game $game,
        private User $user
    ) {}

    public function execute(): self
    {
        $username = $this->user->pivot['player_name'];
        $statsCollection = new Collection;

        $player = collect($this->game->data['players'])
            ->first(function ($player) use ($username) {
                return $player['name'] === $username;
            });

        if ($player) {
            $statsCollection->put('faction', $player['faction']);
            $statsCollection->put('side', $player['side']);
            $statsCollection->put('moneySpent', $player['moneySpent']);
            $statsCollection->put('unitsCreated', $player['unitsCreated']);
            $statsCollection->put('buildingsBuilt', $player['buildingsBuilt']);
            $statsCollection->put('upgradesBuilt', $player['upgradesBuilt']);
            $statsCollection->put('powersUsed', $player['powersUsed']);
        }

        $this->stat->giveStats($statsCollection);

        return $this->setSuccessful();
    }
}
