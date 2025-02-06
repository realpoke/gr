<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Models\Game;
use App\Models\Pivots\GameUserPivot;

class SetGamePivotWinnerAction extends BaseAction
{
    public function __construct(private Game $game) {}

    public function execute(): self
    {
        if ($this->game->users()->wherePivot('winner', true)->exists()) {
            return $this->setFailed('Game already has winners set');
        }

        $playerWinners = collect($this->game->data['players'])->filter(function ($player) {
            return $player['win'] ?? false;
        });

        if ($playerWinners->count() < 1) {
            return $this->setFailed('Game does not have any winners');
        }

        GameUserPivot::where('game_id', $this->game->id)
            ->whereIn('player_name', $playerWinners->pluck('name'))
            ->update(['winner' => true]);

        return $this->setSuccessful();
    }
}
