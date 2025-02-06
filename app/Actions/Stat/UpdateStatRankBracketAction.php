<?php

namespace App\Actions\Stat;

use App\Actions\BaseAction;
use App\Enums\Rank\RankBracketEnum;
use App\Models\Stat;

class UpdateStatRankBracketAction extends BaseAction
{
    public function __construct(private Stat $stat) {}

    public function execute(): self
    {
        if ($this->stat->elo === null) {
            $eloBracket = RankBracketEnum::UNRANKED;
        } else {
            $eloBracket = RankBracketEnum::fromElo($this->stat->elo);
        }

        if ($eloBracket === null) {
            return $this->setFailed('Stat elo is not in a valid bracket');
        }

        $this->stat->bracket = $eloBracket;
        if (! $this->stat->save()) {
            return $this->setFailed('Failed to save stat');
        }

        return $this->setSuccessful();
    }
}
