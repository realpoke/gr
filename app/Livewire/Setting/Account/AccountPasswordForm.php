<?php

namespace App\Livewire\Setting\Account;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class AccountPasswordForm extends Form
{
    use AuthRules;

    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public bool $logoutOther = false;

    public function rules(): array
    {
        return [
            'currentPassword' => self::useCurrentPassword(),
            'password' => self::setPassword(),
            'logoutOther' => self::useLogoutOther(),
        ];
    }
}
