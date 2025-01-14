<?php

namespace App\Livewire\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class SendPasswordResetLinkForm extends Form
{
    use AuthRules;

    public string $email = '';

    public function rules(): array
    {
        return [
            'email' => self::useEmail(),
        ];
    }
}
