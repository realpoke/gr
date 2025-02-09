<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Models\Badge;
use App\Models\Game;

class GameMvpBadgeGiverAction extends BaseAction
{
    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $players = collect($this->game->data['players']);

        $mvpName = $players->sortByDesc('countOrders')->first()['name'] ?? null;
        if (! $mvpName) {
            return $this->setFailed('No MVP player found');
        }

        $mvpUser = $this->game->users()->wherePivot('player_name', $mvpName)->first();
        if (! $mvpUser) {
            return $this->setFailed('User '.$mvpName.' not found');
        }

        $mvpBadge = Badge::firstWhere('name', 'badge.name.mvp');
        if (! $mvpBadge) {
            return $this->setFailed('MVP badge not found');
        }

        if (! $mvpUser->giveBadge($mvpBadge)) {
            return $this->setFailed('Failed to give MVP badge');
        }

        return $this->setSuccessful();
    }
}
