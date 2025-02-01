<?php

namespace App\Factories;

use App\Actions\Elo\EloCalculatorFreeForAllAction;
use App\Actions\Elo\EloCalculatorOneOnOneAction;
use App\Actions\Elo\EloCalculatorTeamAction;
use App\Contracts\EloCalculatorContract;
use App\Enums\Game\GameModeEnum;
use App\Models\Game;

class EloCalculatorFactory
{
    public function __invoke(Game $game): ?EloCalculatorContract
    {
        return match ($game->type->getGameMode()) {
            GameModeEnum::ONE_ON_ONE => new EloCalculatorOneOnOneAction,
            GameModeEnum::TEAM => new EloCalculatorTeamAction,
            GameModeEnum::FREE_FOR_ALL => new EloCalculatorFreeForAllAction,
            default => null,
        };
    }
}
