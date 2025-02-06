<?php

namespace App\Actions\Elo;

use App\Actions\BaseAction;
use App\Contracts\EloCalculatorContract;
use App\Enums\Rank\RankTimeFrameEnum;
use App\Models\Game;
use App\Models\Period;

class EloCalculatorTeamAction extends BaseAction implements EloCalculatorContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $users = $this->game->users;

        $winningTeamUsers = $users->filter(function ($user) {
            return $user->pivot->winner === true;
        });

        $losingTeamUsers = $users->filter(function ($user) {
            return $user->pivot->winner === false;
        });

        $period = Period::getFirstOrCreateByGameModeAndTimeFrame($this->game->type->getGameMode(), RankTimeFrameEnum::ALL);

        $winningTeamAvgElo = $winningTeamUsers->avg(function ($user) use ($period) {
            $stats = $user->getOrCreateCurrentStatsForPeriod($period);

            return $stats->elo ?? 1500;
        });

        $losingTeamAvgElo = $losingTeamUsers->avg(function ($user) use ($period) {
            $stats = $user->getOrCreateCurrentStatsForPeriod($period);

            return $stats->elo ?? 1500;
        });

        $calculator = new ChessEloCalculationAction((int) round($winningTeamAvgElo), (int) round($losingTeamAvgElo));
        $calculator->handle();

        if ($calculator->failed()) {
            return $this->setFailed('Failed to calculate ELO change: '.$calculator->getErrorMessage());
        }

        foreach ($winningTeamUsers as $user) {
            $eloGain = $calculator->getWinnerGainedElo();
            $this->game->users()->updateExistingPivot($user->id, [
                'elo_change' => $eloGain,
            ]);
        }

        foreach ($losingTeamUsers as $user) {
            $eloLoss = $calculator->getLoserLostElo();
            $this->game->users()->updateExistingPivot($user->id, [
                'elo_change' => $eloLoss,
            ]);
        }

        $this->game->load('users');

        return $this->setSuccessful();
    }
}
