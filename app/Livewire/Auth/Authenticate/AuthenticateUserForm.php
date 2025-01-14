<?php

namespace App\Livewire\Auth\Authenticate;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class AuthenticateUserForm extends Form
{
    use AuthRules;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public ?string $twoFactorCode = null;

    public function rules(): array
    {
        return [
            'email' => self::useEmail(),
            'password' => self::usePassword(),
            'remember' => self::useRemember(),
            'twoFactorCode' => self::useTwoFactorCode(),
        ];
    }
}
