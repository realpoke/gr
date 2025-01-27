<?php

namespace App\Traits\Rules;

trait ClaimRules
{
    public static function claimWithinMinutes(): int
    {
        return 120;
    }

    public static function usePrivate(): array
    {
        return ['boolean'];
    }

    public static function computerClaimLimit(): int
    {
        return 2;
    }
}
