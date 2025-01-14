<?php

namespace App\Livewire\Setting\Account;

use App\Models\User;
use App\Traits\Rules\AuthRules;
use Livewire\Attributes\Locked;
use Livewire\Form;

class AccountEmailForm extends Form
{
    use AuthRules;

    public string $email = '';

    public string $password = '';

    #[Locked()]
    public User $user;

    public function rules(): array
    {
        return [
            'email' => self::setEmail($this->user),
            'password' => self::useCurrentPassword(),
        ];
    }
}
