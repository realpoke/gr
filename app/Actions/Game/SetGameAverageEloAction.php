<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Enums\Rank\RankBracketEnum;
use App\Enums\Rank\RankTimeFrameEnum;
use App\Models\Game;
use App\Models\Period;

class SetGameAverageEloAction extends BaseAction
{
    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $period = Period::getFirstOrCreateByGameModeAndTimeFrame($this->game->type->getGameMode(), RankTimeFrameEnum::ALL);
        $eloAverage = $this->game->users->map(function ($user) use ($period) {
            $stats = $user->getOrCreateCurrentStatsForPeriod($period);

            return $stats->elo;
        })->avg();

        $this->game->update([
            'elo_average' => $eloAverage,
            'bracket' => $eloAverage > 0 ? RankBracketEnum::fromElo($eloAverage) : RankBracketEnum::UNRANKED,
        ]);

        return $this->setSuccessful();
    }
}
