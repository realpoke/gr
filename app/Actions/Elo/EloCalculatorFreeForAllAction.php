<?php

namespace App\Actions\Elo;

use App\Actions\BaseAction;
use App\Contracts\EloCalculatorContract;
use App\Enums\Rank\RankTimeFrameEnum;
use App\Models\Game;
use App\Models\Period;

class EloCalculatorFreeForAllAction extends BaseAction implements EloCalculatorContract
{
    public function getGame(): Game
    {
        return $this->game;
    }

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $users = $this->game->users;
        $period = Period::getFirstOrCreateByGameModeAndTimeFrame($this->game->type->getGameMode(), RankTimeFrameEnum::ALL);

        // Sort players by eliminated position (1 = first eliminated, highest number = winner)
        $sortedPlayers = $users->sortByDesc('pivot.eliminated_position')->values();

        foreach ($sortedPlayers as $i => $player) {
            $totalEloChange = 0;
            $matchupsCount = 0;

            $playerStats = $player->getOrCreateCurrentStatsForPeriod($period);
            $playerElo = $playerStats->elo ?? 1500;

            // Calculate ELO changes against all players
            foreach ($sortedPlayers as $j => $opponent) {
                if ($i === $j) {
                    continue;
                }

                $opponentStats = $opponent->getOrCreateCurrentStatsForPeriod($period);
                $opponentElo = $opponentStats->elo ?? 1500;

                // Player placed higher (later elimination / higher number)
                if ($player->pivot->eliminated_position > $opponent->pivot->eliminated_position) {
                    $calculation = new ChessEloCalculationAction($playerElo, $opponentElo);
                    $calculation->handle();

                    if ($calculation->failed()) {
                        return $this->setFailed('Failed to calculate ELO change: '.$calculation->getErrorMessage());
                    }

                    $totalEloChange += $calculation->getWinnerGainedElo();
                    $matchupsCount++;
                }
                // Player placed lower (earlier elimination / lower number)
                else {
                    $calculation = new ChessEloCalculationAction($opponentElo, $playerElo);
                    $calculation->handle();

                    if ($calculation->failed()) {
                        return $this->setFailed('Failed to calculate ELO change: '.$calculation->getErrorMessage());
                    }

                    $totalEloChange += $calculation->getLoserLostElo();
                    $matchupsCount++;
                }
            }

            // Calculate average ELO change from all matchups
            if ($matchupsCount > 0) {
                $finalEloChange = (int) round($totalEloChange / $matchupsCount);

                $this->game->users()->updateExistingPivot($player->id, [
                    'elo_change' => $finalEloChange,
                ]);
            }
        }

        $this->game->load('users');

        return $this->setSuccessful();
    }
}
