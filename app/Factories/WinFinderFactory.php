<?php

namespace App\Factories;

use App\Actions\Game\WinFinderFreeForAllAction;
use App\Actions\Game\WinFinderOneOnOneAction;
use App\Actions\Game\WinFinderTeamAction;
use App\Contracts\WinFinderContract;
use App\Enums\Game\GameTypeEnum;
use App\Models\Game;

class WinFinderFactory
{
    public function __invoke(Game $game): ?WinFinderContract
    {
        return match ($game->type) {
            GameTypeEnum::ONE_ON_ONE => new WinFinderOneOnOneAction($game),
            GameTypeEnum::TWO_ON_TWO,
            GameTypeEnum::THREE_ON_THREE,
            GameTypeEnum::FOUR_ON_FOUR => new WinFinderTeamAction($game),
            GameTypeEnum::FREE_FOR_ALL_THREE,
            GameTypeEnum::FREE_FOR_ALL_FOUR,
            GameTypeEnum::FREE_FOR_ALL_FIVE,
            GameTypeEnum::FREE_FOR_ALL_SIX,
            GameTypeEnum::FREE_FOR_ALL_SEVEN,
            GameTypeEnum::FREE_FOR_ALL_EIGHT => new WinFinderFreeForAllAction($game),
            default => null,
        };
    }
}
