<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum SideEnum: string
{
    use EnumArray;

    case OBSERVER = 'observer';
    case RANDOM = 'random';

    case USA = 'usa';
    case CHINA = 'china';
    case GLA = 'gla';

    case USA_SUPERWEAPON = 'usa-superweapon';
    case USA_LAZR = 'usa-lazr';
    case USA_AIRFORCE = 'usa-airforce';

    case CHINA_TANK = 'china-tank';
    case CHINA_INFANTRY = 'china-infantry';
    case CHINA_NUKE = 'china-nuke';

    case GLA_TOXIN = 'gla-toxin';
    case GLA_DEMO = 'gla-demo';
    case GLA_STEALTH = 'gla-stealth';

    case UNKNOWN = 'unknown';

    public function isValidSide(): bool
    {
        return $this->value !== 'unknown';
    }

    public function prettyName(): string
    {
        return match ($this) {
            self::OBSERVER => __('enum.side.observer'),
            self::RANDOM => __('enum.side.random'),
            self::USA => __('enum.side.usa'),
            self::CHINA => __('enum.side.china'),
            self::GLA => __('enum.side.gla'),
            self::USA_SUPERWEAPON => __('enum.side.usa-superweapon'),
            self::USA_LAZR => __('enum.side.usa-lazr'),
            self::USA_AIRFORCE => __('enum.side.usa-airforce'),
            self::CHINA_TANK => __('enum.side.china-tank'),
            self::CHINA_INFANTRY => __('enum.side.china-infantry'),
            self::CHINA_NUKE => __('enum.side.china-nuke'),
            self::GLA_TOXIN => __('enum.side.gla-toxin'),
            self::GLA_DEMO => __('enum.side.gla-demo'),
            self::GLA_STEALTH => __('enum.side.gla-stealth'),
            self::UNKNOWN => __('enum.side.unknown'),
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }
}
