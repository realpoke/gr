<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum BadgeTypeEnum: string
{
    use EnumArray;

    case PERMISSION = 'permission';
    case UNIQUE = 'unique';
    case SINCE = 'since';
    case ADDITIONAL = 'additional';
    case TIMESTAMP = 'timestamp';
}
