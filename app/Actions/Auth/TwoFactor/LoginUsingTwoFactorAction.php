<?php

namespace App\Actions\Auth\TwoFactor;

use App\Actions\BaseAction;
use App\Livewire\Auth\Authenticate\AuthenticateUserForm;
use App\Models\User;
use App\Traits\WithLimits;
use PragmaRX\Google2FA\Google2FA;

class LoginUsingTwoFactorAction extends BaseAction
{
    use WithLimits;

    public function __construct(private User $user, private AuthenticateUserForm $form) {}

    public function execute(): self
    {
        if (empty($this->user->two_factor_secret)) {
            return $this->setFailed(__('auth.two-factor.not-enabled'));
        }

        $this->limitAction('login-using-two-factor', forField: 'form.twoFactorCode');

        $this->form->validateOnly('twoFactorCode');

        $twoFactor = new Google2FA;
        $validKey = $twoFactor->verifyKey($this->user->two_factor_secret, $this->form->twoFactorCode);

        if ($validKey == false) {
            return $this->setFailed(__('auth.two-factor.invalid'));
        }

        return $this->setSuccessful();
    }
}
