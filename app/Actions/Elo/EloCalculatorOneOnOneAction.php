<?php

namespace App\Actions\Elo;

use App\Actions\BaseAction;
use App\Contracts\EloCalculatorContract;
use App\Enums\Rank\RankTimeFrameEnum;
use App\Models\Game;
use App\Models\Period;

class EloCalculatorOneOnOneAction extends BaseAction implements EloCalculatorContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $users = $this->game->users;

        $winner = $users->first(function ($user) {
            return $user->pivot->winner === true;
        });

        $loser = $users->first(function ($user) {
            return $user->pivot->winner === false;
        });

        if (! $winner || ! $loser) {
            throw new \RuntimeException('Cannot determine winner and loser for game '.$this->game->id);
        }

        $period = Period::fromGameModeAndTimeFrame($this->game->type->getGameMode(), RankTimeFrameEnum::ALL);

        $winnerStats = $winner->getOrCreateCurrentStatsForPeriod($period);
        $loserStats = $loser->getOrCreateCurrentStatsForPeriod($period);

        $calculator = new ChessEloCalculationAction($winnerStats->elo ?? 1500, $loserStats->elo ?? 1500);
        $calculator->handle();

        if ($calculator->failed()) {
            return $this->setFailed('Failed to calculate ELO change: '.$calculator->getErrorMessage());
        }

        $this->game->users()->updateExistingPivot($winner->id, [
            'elo_change' => $calculator->getWinnerGainedElo(),
        ]);

        $this->game->users()->updateExistingPivot($loser->id, [
            'elo_change' => $calculator->getLoserLostElo(),
        ]);

        return $this->setSuccessful();
    }
}
