<?php

namespace App\Actions\Stat;

use App\Actions\BaseAction;
use App\Models\Game;
use App\Models\Stat;
use App\Models\User;

class StatAddGamePlayedAction extends BaseAction
{
    public function __construct(
        private Stat $stat,
        private Game $game,
        private User $user
    ) {}

    public function execute(): self
    {
        $players = collect($this->game->data['players'])
            ->filter(fn ($player) => $player['isPlaying']);

        $pivotData = $this->user->pivot;

        $playerData = $players->firstWhere('name', $pivotData->player_name);
        if (! $playerData) {
            return $this->setFailed('Could not find player data for user '.$this->user->id);
        }

        $isWinner = $playerData['win'] ?? false;

        $this->stat->games++;

        if ($isWinner) {
            $this->stat->wins++;
            $this->stat->streak = ($this->stat->streak ?? 0) >= 0 ?
                ($this->stat->streak ?? 0) + 1 : 1;
        } else {
            $this->stat->losses++;
            $this->stat->streak = ($this->stat->streak ?? 0) <= 0 ?
                ($this->stat->streak ?? 0) - 1 : -1;
        }

        $this->stat->win_percentage = (float) (($this->stat->wins / $this->stat->games) * 100);

        if (! $this->stat->save()) {
            $this->setFailed('Failed to save stat: '.$this->stat->getErrorMessage());
        }

        $this->user->last_game_at = now();
        if (! $this->user->save()) {
            $this->setFailed('Failed to save user: '.$this->user->getErrorMessage());
        }

        return $this->setSuccessful();
    }
}
