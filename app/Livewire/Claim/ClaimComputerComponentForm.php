<?php

namespace App\Livewire\Claim;

use App\Traits\Rules\AuthRules;
use App\Traits\Rules\ClaimRules;
use Livewire\Form;

class ClaimComputerComponentForm extends Form
{
    use AuthRules, ClaimRules;

    public string $password = '';

    public bool $private = false;

    public function rules(): array
    {
        return [
            'password' => self::useCurrentPassword(),
            'private' => self::usePrivate(),
        ];
    }
}
