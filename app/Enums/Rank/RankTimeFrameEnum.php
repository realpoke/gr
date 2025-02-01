<?php

namespace App\Enums\Rank;

use App\Traits\EnumArray;

enum RankTimeFrameEnum: string
{
    use EnumArray;

    case ALL = 'all';
    case YEARLY = 'yearly';
    case MONTHLY = 'monthly';

    public function prettyName(): string
    {
        return match ($this) {
            self::ALL => __('enum.all-time'),
            self::YEARLY => __('enum.yearly'),
            self::MONTHLY => __('enum.monthly'),
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }
}
