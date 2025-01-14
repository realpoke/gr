<?php

namespace App\Livewire\Setting\Profile;

use App\Models\User;
use App\Traits\Rules\AuthRules;
use Livewire\Attributes\Locked;
use Livewire\Form;

class ProfileUsernameForm extends Form
{
    use AuthRules;

    public string $username = '';

    #[Locked()]
    public User $user;

    public function rules(): array
    {
        return [
            'username' => self::setUsername(),
        ];
    }
}
