<?php

namespace App\Enums\Game;

use App\Traits\EnumArray;

enum GameTypeEnum: string
{
    use EnumArray;

    case SOLO = 'solo';

    case ONE_ON_ONE = 'one-on-one';

    case TWO_ON_TWO = 'two-on-two';
    case THREE_ON_THREE = 'three-on-three';
    case FOUR_ON_FOUR = 'four-on-four';

    case FREE_FOR_ALL_THREE = 'free-for-all-3';
    case FREE_FOR_ALL_FOUR = 'free-for-all-4';
    case FREE_FOR_ALL_FIVE = 'free-for-all-5';
    case FREE_FOR_ALL_SIX = 'free-for-all-6';
    case FREE_FOR_ALL_SEVEN = 'free-for-all-7';
    case FREE_FOR_ALL_EIGHT = 'free-for-all-8';

    case CO_OP_TWO = 'co-op-2';
    case CO_OP_THREE = 'co-op-3';
    case CO_OP_FOUR = 'co-op-4';
    case CO_OP_FIVE = 'co-op-5';
    case CO_OP_SIX = 'co-op-6';
    case CO_OP_SEVEN = 'co-op-7';
    case CO_OP_EIGHT = 'co-op-8';

    case MULTI_TEAM_THREE = 'multi-team-3';
    case MULTI_TEAM_FOUR = 'multi-team-4';

    case UNSUPPORTED = 'unsupported';

    public function getGameMode(): GameModeEnum
    {
        return match ($this) {
            self::SOLO => GameModeEnum::SOLO,
            self::CO_OP_TWO,
            self::CO_OP_THREE,
            self::CO_OP_FOUR,
            self::CO_OP_FIVE,
            self::CO_OP_SIX,
            self::CO_OP_SEVEN,
            self::CO_OP_EIGHT => GameModeEnum::CO_OP,
            self::MULTI_TEAM_THREE,
            self::MULTI_TEAM_FOUR => GameModeEnum::MULTI_TEAM,
            self::ONE_ON_ONE => GameModeEnum::ONE_ON_ONE,
            self::TWO_ON_TWO,
            self::THREE_ON_THREE,
            self::FOUR_ON_FOUR => GameModeEnum::TEAM,
            self::FREE_FOR_ALL_THREE,
            self::FREE_FOR_ALL_FOUR,
            self::FREE_FOR_ALL_FIVE,
            self::FREE_FOR_ALL_SIX,
            self::FREE_FOR_ALL_SEVEN,
            self::FREE_FOR_ALL_EIGHT => GameModeEnum::FREE_FOR_ALL,
            default => GameModeEnum::UNSUPPORTED,
        };
    }

    public function isFreeForAll(): bool
    {
        return in_array($this, [
            self::FREE_FOR_ALL_THREE,
            self::FREE_FOR_ALL_FOUR,
            self::FREE_FOR_ALL_FIVE,
            self::FREE_FOR_ALL_SIX,
            self::FREE_FOR_ALL_SEVEN,
            self::FREE_FOR_ALL_EIGHT,
        ]);
    }

    public function playersShouldBePlaying(): ?int
    {
        return match ($this) {
            self::SOLO => 1,
            self::CO_OP_TWO,
            self::ONE_ON_ONE => 2,
            self::CO_OP_THREE,
            self::FREE_FOR_ALL_THREE => 3,
            self::CO_OP_FOUR,
            self::FREE_FOR_ALL_FOUR,
            self::TWO_ON_TWO => 4,
            self::FREE_FOR_ALL_FIVE,
            self::CO_OP_FIVE => 5,
            self::THREE_ON_THREE,
            self::CO_OP_SIX,
            self::FREE_FOR_ALL_SIX => 6,
            self::CO_OP_SEVEN,
            self::FREE_FOR_ALL_SEVEN => 7,
            self::MULTI_TEAM_THREE => 6,
            self::FREE_FOR_ALL_EIGHT,
            self::CO_OP_EIGHT,
            self::FOUR_ON_FOUR,
            self::MULTI_TEAM_FOUR => 8,
            default => null,
        };
    }

    public function isValidGameType(): bool
    {
        return $this->getGameMode()->isValidGameMode();
    }

    public static function getValidGameTypes(): array
    {
        return [
            self::ONE_ON_ONE,
            self::TWO_ON_TWO,
            self::THREE_ON_THREE,
            self::FOUR_ON_FOUR,
            self::FREE_FOR_ALL_THREE,
            self::FREE_FOR_ALL_FOUR,
            self::FREE_FOR_ALL_FIVE,
            self::FREE_FOR_ALL_SIX,
            self::FREE_FOR_ALL_SEVEN,
            self::FREE_FOR_ALL_EIGHT,
        ];
    }

    public static function validGameTypeStrings(): array
    {
        return [
            '1v1',
            '1v1v1',
            '1v1v1v1',
            '1v1v1v1v1',
            '1v1v1v1v1v1',
            '1v1v1v1v1v1v1',
            '1v1v1v1v1v1v1v1',
            '2v2',
            '3v3',
            '4v4',
        ];
    }

    public function prettyName(): string
    {
        return match ($this) {
            self::SOLO => __('enum.game-type.solo'),
            self::CO_OP_TWO => __('enum.game-type.co-op-2'),
            self::CO_OP_THREE => __('enum.game-type.co-op-3'),
            self::CO_OP_FOUR => __('enum.game-type.co-op-4'),
            self::CO_OP_FIVE => __('enum.game-type.co-op-5'),
            self::CO_OP_SIX => __('enum.game-type.co-op-6'),
            self::CO_OP_SEVEN => __('enum.game-type.co-op-7'),
            self::CO_OP_EIGHT => __('enum.game-type.co-op-8'),
            self::MULTI_TEAM_THREE => __('enum.game-type.multi-team-3'),
            self::MULTI_TEAM_FOUR => __('enum.game-type.multi-team-4'),
            self::ONE_ON_ONE => __('enum.game-type.1-v-1'),
            self::TWO_ON_TWO => __('enum.game-type.2-v-2'),
            self::THREE_ON_THREE => __('enum.game-type.3-v-3'),
            self::FOUR_ON_FOUR => __('enum.game-type.4-v-4'),
            self::FREE_FOR_ALL_THREE => __('enum.game-type.ffa-3'),
            self::FREE_FOR_ALL_FOUR => __('enum.game-type.ffa-4'),
            self::FREE_FOR_ALL_FIVE => __('enum.game-type.ffa-5'),
            self::FREE_FOR_ALL_SIX => __('enum.game-type.ffa-6'),
            self::FREE_FOR_ALL_SEVEN => __('enum.game-type.ffa-7'),
            self::FREE_FOR_ALL_EIGHT => __('enum.game-type.ffa-8'),
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }
}
