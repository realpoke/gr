<?php

namespace App\Enums\Game;

use App\Traits\EnumArray;

enum GameModeEnum: string
{
    use EnumArray;

    case SOLO = 'solo';
    case ONE_ON_ONE = '1v1';
    case TEAM = 'team';
    case FREE_FOR_ALL = 'free-for-all';
    case CO_OP = 'co-op';
    case MULTI_TEAM = 'multi-team';
    case UNSUPPORTED = 'unsupported';

    public function isValidGameMode(): bool
    {
        return in_array($this, $this::validGameModes());
    }

    public static function validGameModes(): array
    {
        return [
            self::ONE_ON_ONE,
            self::TEAM,
            self::FREE_FOR_ALL,
        ];
    }

    public function prettyName(): string
    {
        return match ($this) {
            self::SOLO => __('enum.game-mode.solo'),
            self::ONE_ON_ONE => __('enum.game-mode.1-v-1'),
            self::TEAM => __('enum.game-mode.team-v-team'),
            self::FREE_FOR_ALL => __('enum.game-mode.ffa'),
            self::CO_OP => __('enum.game-mode.co-op'),
            self::MULTI_TEAM => __('enum.game-mode.multi-team'),
            self::UNSUPPORTED => __('enum.game-mode.unsupported'),
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }
}
