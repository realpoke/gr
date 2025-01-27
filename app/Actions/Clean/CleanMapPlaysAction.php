<?php

namespace App\Actions\Clean;

use App\Actions\BaseAction;
use App\Models\Map;

class CleanMapPlaysAction extends BaseAction
{
    public function __construct(private bool $monthly = false, private bool $weekly = false) {}

    public function execute(): self
    {
        if ($this->monthly) {
            Map::where('plays_monthly', '>', 0)
                ->update(['plays_monthly' => 0]);
        }

        if ($this->weekly) {
            Map::where('plays_weekly', '>', 0)
                ->update(['plays_weekly' => 0]);
        }

        return $this->setSuccessful();
    }
}
