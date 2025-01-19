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
        return $this->value !== 'observer' && $this->value !== 'random' && $this->value !== 'unknown';
    }
}
