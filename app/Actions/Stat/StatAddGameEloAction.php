<?php

namespace App\Actions\Stat;

use App\Actions\BaseAction;
use App\Models\Stat;
use App\Models\User;
use Exception;

class StatAddGameEloAction extends BaseAction
{
    public function __construct(
        private Stat $stat,
        private User $user
    ) {}

    public function execute(): self
    {
        $pivotData = $this->user->pivot;
        if (! is_int($pivotData->elo_change)) {
            throw new Exception('Invalid Elo change for user: '.$this->user->id);
        }

        if ($pivotData->elo_change >= 0) {
            $this->user->giveEloToStat($pivotData->elo_change, $this->stat);
        } else {
            $this->user->takeEloFromStat($pivotData->elo_change, $this->stat);
        }

        return $this->setSuccessful();
    }
}
