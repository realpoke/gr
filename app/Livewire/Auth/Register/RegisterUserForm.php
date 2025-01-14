<?php

namespace App\Livewire\Auth\Register;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class RegisterUserForm extends Form
{
    use AuthRules;

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public bool $terms = false;

    public function rules(): array
    {
        return [
            'username' => self::setUsername(),
            'email' => self::setEmail(),
            'password' => self::setPassword(),
            'terms' => self::setTerms(),
        ];
    }
}
