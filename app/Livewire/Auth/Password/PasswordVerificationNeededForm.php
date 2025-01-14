<?php

namespace App\Livewire\Auth\Password;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class PasswordVerificationNeededForm extends Form
{
    use AuthRules;

    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => self::usePassword(),
        ];
    }
}
