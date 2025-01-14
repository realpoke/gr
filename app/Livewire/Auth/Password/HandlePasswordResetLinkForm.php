<?php

namespace App\Livewire\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class HandlePasswordResetLinkForm extends Form
{
    use AuthRules;

    public string $password = '';

    public string $passwordConfirmation = '';

    public function rules(): array
    {
        return [
            'password' => self::setPassword(),
        ];
    }
}
