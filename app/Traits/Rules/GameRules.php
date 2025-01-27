<?php

namespace App\Traits\Rules;

use Carbon\CarbonInterval;

trait GameRules
{
    public static function minimumGameInterval(): CarbonInterval
    {
        return CarbonInterval::seconds(90);
    }

    public static function minimumGenToolVersion(): int
    {
        return 89;
    }
}
