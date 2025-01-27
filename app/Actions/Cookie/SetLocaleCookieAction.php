<?php

namespace App\Actions\Cookie;

use App\Actions\BaseAction;
use App\Enums\LanguageEnum;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLocaleCookieAction extends BaseAction
{
    public function __construct(private LanguageEnum|string $locale) {}

    public function execute(): self
    {
        if (is_string($this->locale)) {
            $newLocale = LanguageEnum::tryFrom($this->locale);
        }

        if (! $newLocale) {
            $newLocale = LanguageEnum::default();
        }

        Cookie::queue('preferred_language', $newLocale->value);

        App::setLocale($newLocale->value);

        return $this->setSuccessful();
    }
}
