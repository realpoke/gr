<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum PlayerTypeEnum: string
{
    use EnumArray;

    case HUMAN = 'h';
    case BOT = 'c';
    case OBSERVER = 'o';

    public function isHuman(): bool
    {
        return $this->value === self::HUMAN->value;
    }

    public function isBot(): bool
    {
        return $this->value === self::BOT->value;
    }

    public function isObserver(): bool
    {
        return $this->value === self::OBSERVER->value;
    }
}
