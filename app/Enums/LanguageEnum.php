<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum LanguageEnum: string
{
    use EnumArray;

    case ENGLISH = 'en';

    public function getName(): string
    {
        return match ($this) {
            self::ENGLISH => 'English',
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }

    public static function default(): self
    {
        return self::tryFrom(app()->getLocale()) ??
            self::tryFrom(app()->getFallbackLocale()) ??
            self::ENGLISH;
    }
}
