<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum PlayerTypeEnum: string
{
    use EnumArray;

    case HUMAN = 'h';
    case BOT = 'c';

    public function isHuman(): bool
    {
        return $this->value === self::HUMAN->value;
    }

    public function isBot(): bool
    {
        return $this->value === self::BOT->value;
    }
}
