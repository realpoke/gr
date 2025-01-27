<?php

namespace App\Livewire\Partials;

use App\Actions\Cookie\SetLocaleCookieAction;
use App\Enums\LanguageEnum;
use Flux\Flux;
use Livewire\Component;

class LanguageSelectComponent extends Component
{
    public LanguageEnum $language;

    public function updatedLanguage(LanguageEnum $language)
    {
        $localeSetter = new SetLocaleCookieAction($language);
        $localeSetter->handle();

        if ($localeSetter->failed()) {
            Flux::toast(__('toast.language-failed', ['language' => $language->getName()]));
        }

        Flux::toast(__('toast.language-updated', ['language' => $language->getName()]));

        $this->redirect(request()->header('Referer'), navigate: true);
    }

    public function mount()
    {
        $locale = LanguageEnum::tryFrom(app()->getLocale());
        if (! $locale) {
            $locale = LanguageEnum::default();
        }

        $this->language = $locale;
    }
}
