<?php

namespace App\Enums\Game;

use App\Traits\EnumArray;

enum GameStatusEnum: string
{
    use EnumArray;

    case AWAITING = 'awaiting';
    case PROCESSING = 'processing';
    case RANKED = 'ranked';
    case UNRANKED = 'unranked';
    case INVALID = 'invalid';

    public function getStatusBadgeColor(): string
    {
        return match ($this) {
            self::PROCESSING => 'blue',
            self::RANKED => 'green',
            self::UNRANKED => 'green',
            self::INVALID => 'zinc',
            default => 'zinc'
        };
    }

    public function prettyName(): string
    {
        return match ($this) {
            self::AWAITING => __('enum.game-status.awaiting'),
            self::PROCESSING => __('enum.game-status.processing'),
            self::RANKED => __('enum.game-status.ranked'),
            self::UNRANKED => __('enum.game-status.unranked'),
            self::INVALID => __('enum.game-status.invalid'),
            default => ucfirst(str_replace('_', ' ', $this->value)),
        };
    }
}
