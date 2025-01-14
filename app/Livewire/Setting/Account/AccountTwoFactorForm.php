<?php

namespace App\Livewire\Setting\Account;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class AccountTwoFactorForm extends Form
{
    use AuthRules;

    public string $password = '';

    public string $twoFactorCode = '';

    public bool $logoutOther = false;

    public function rules(): array
    {
        return [
            'password' => self::useCurrentPassword(),
            'twoFactorCode' => self::useTwoFactorCode(),
            'logoutOther' => self::useLogoutOther(),
        ];
    }
}
