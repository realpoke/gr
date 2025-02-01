<?php

namespace App\Traits;

trait EnumArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function ccases(array $cases): array
    {
        return collect($cases)
            ->map(fn ($case) => $case->value)
            ->filter()
            ->toArray();
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
