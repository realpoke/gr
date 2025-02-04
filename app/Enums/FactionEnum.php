<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum FactionEnum: int
{
    use EnumArray;

    case UNKNOWN = -3;

    case OBSERVER = -2;
    case RANDOM = -1;

    case USA = 2;
    case CHINA = 3;
    case GLA = 4;

    case USA_SUPERWEAPON = 5;
    case USA_LAZR = 6;
    case USA_AIRFORCE = 7;

    case CHINA_TANK = 8;
    case CHINA_INFANTRY = 9;
    case CHINA_NUKE = 10;

    case GLA_TOXIN = 11;
    case GLA_DEMO = 12;
    case GLA_STEALTH = 13;

    public function getSide(): SideEnum
    {
        return match ($this) {
            self::OBSERVER => SideEnum::OBSERVER,
            self::RANDOM => SideEnum::RANDOM,
            self::USA => SideEnum::USA,
            self::CHINA => SideEnum::CHINA,
            self::GLA => SideEnum::GLA,
            self::USA_SUPERWEAPON => SideEnum::USA_SUPERWEAPON,
            self::USA_LAZR => SideEnum::USA_LAZR,
            self::USA_AIRFORCE => SideEnum::USA_AIRFORCE,
            self::CHINA_TANK => SideEnum::CHINA_TANK,
            self::CHINA_INFANTRY => SideEnum::CHINA_INFANTRY,
            self::CHINA_NUKE => SideEnum::CHINA_NUKE,
            self::GLA_TOXIN => SideEnum::GLA_TOXIN,
            self::GLA_DEMO => SideEnum::GLA_DEMO,
            self::GLA_STEALTH => SideEnum::GLA_STEALTH,
            default => SideEnum::UNKNOWN,
        };
    }

    public static function baseFactions(): array
    {
        return [
            self::USA,
            self::CHINA,
            self::GLA,
            self::RANDOM,
        ];
    }

    public static function baseFactionsValue(): array
    {
        return array_column(self::baseFactions(), 'value');
    }

    public function baseFaction(): FactionEnum
    {
        return match ($this) {
            self::USA_AIRFORCE,
            self::USA_LAZR,
            self::USA_SUPERWEAPON,
            self::USA => self::USA,
            self::CHINA_INFANTRY,
            self::CHINA_NUKE,
            self::CHINA_TANK,
            self::CHINA => self::CHINA,
            self::GLA_DEMO,
            self::GLA_STEALTH,
            self::GLA_TOXIN,
            self::GLA => self::GLA,
            self::RANDOM => self::RANDOM,
            default => self::UNKNOWN,
        };
    }

    public function isPlaying(): bool
    {
        return $this->value >= -1;
    }

    public function isObserver(): bool
    {
        return $this->value === -2;
    }
}
