<?php

namespace App\Livewire\Setting\Account;

use App\Traits\Rules\AuthRules;
use Livewire\Form;

class AccountLogoutOtherDevicesForm extends Form
{
    use AuthRules;

    public string $password = '';

    public function rules(): array
    {
        return [
            'password' => self::useCurrentPassword(),
        ];
    }
}
