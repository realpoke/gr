<?php

namespace App\Actions\Game;

use App\Actions\BaseAction;
use App\Enums\Game\GameStatusEnum;
use App\Enums\Game\GameTypeEnum;
use App\Models\Game;
use App\Models\Map;
use App\Traits\Rules\GameRules;

class SetGameRankedAction extends BaseAction
{
    use GameRules;

    public function __construct(private Game $game) {}

    public function execute(): self
    {
        $map = $this->game->map;

        $rankedMap = $this->isMapRanked($map);
        $mapValidGameType = $this->isGameTypeValidOnMap($this->game->type, $map);

        if ($rankedMap && $mapValidGameType && $this->LongEnough($this->game)) {

            $shouldBeBalanced = $this->shouldGameBeBalanced($this->game);

            if (! $shouldBeBalanced) {
                $this->game->status = GameStatusEnum::RANKED;
            } elseif ($shouldBeBalanced && $this->isGamBalanced($this->game)) {
                $this->game->status = GameStatusEnum::RANKED;
            } else {
                $this->game->status = GameStatusEnum::UNRANKED;
            }
        } else {
            $this->game->status = GameStatusEnum::UNRANKED;
        }

        $this->game->save();

        return $this->setSuccessful();
    }

    private function isMapRanked(Map $map): bool
    {
        return $map->isRanked() ?? false;
    }

    private function isGameTypeValidOnMap(GameTypeEnum $gameType, Map $map): bool
    {
        return collect($map->types)->contains($gameType);
    }

    private function shouldGameBeBalanced(Game $game): bool
    {
        // TODO: implement game balancing
        return true;
    }

    private function isGamBalanced(Game $game): bool
    {
        // TODO: implement game balancing
        return true;
    }

    private function LongEnough(Game $game): bool
    {
        return $game->data['metaData']['gameInterval'] >= self::minimumGameInterval()->seconds;
    }
}
