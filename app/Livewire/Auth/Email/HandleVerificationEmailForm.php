<?php

namespace App\Livewire\Auth\Email;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class HandleVerificationEmailForm extends Form
{
    use AuthRules;

    public string $id = '';

    public string $hash = '';

    public function rules(): array
    {
        return [
            'id' => self::useRequiredString(),
            'hash' => self::useRequiredString(),
        ];
    }
}
