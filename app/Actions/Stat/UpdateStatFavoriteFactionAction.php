<?php

namespace App\Actions\Stat;

use App\Actions\BaseAction;
use App\Models\Game;
use App\Models\Stat;
use App\Models\User;

class UpdateStatFavoriteFactionAction extends BaseAction
{
    public function __construct(
        private Stat $stat,
        private Game $game,
        private User $user
    ) {}

    public function execute(): self
    {
        $gamePlayers = collect($this->game->data['players']);

        $playerData = $gamePlayers->first(function ($player) {
            return $player['name'] === $this->user->pivot->player_name;
        });

        if (! $playerData) {
            return $this->setFailed('Could not find player data for user '.$this->user->id);
        }

        $playerFaction = $playerData['faction'];

        if (! $this->stat->favorite_faction || $playerFaction !== $this->stat->favorite_faction->value) {
            $this->stat->favorite_faction = $this->stat->favoriteBaseFaction();

            if (! $this->stat->save()) {
                return $this->setFailed('Failed to save favorite faction for user '.$this->user->id);
            }
        }

        return $this->setSuccessful();
    }
}
