<?php

namespace App\Actions\Auth\User;

use App\Actions\Auth\TwoFactor\LoginUsingTwoFactorAction;
use App\Actions\BaseAction;
use App\Livewire\Auth\Authenticate\AuthenticateUserForm;
use App\Models\User;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticateUserAction extends BaseAction
{
    use WithLimits;

    private bool $twoFactorRequired = false;

    public function __construct(private AuthenticateUserForm $form) {}

    public function execute(): self
    {
        $this->limitAction('login', forField: 'form.email');

        $this->form->validateOnly('email');
        $this->form->validateOnly('password');
        $this->form->validateOnly('remember');

        $user = User::where('email', $this->form->email)
            ->where('fake', false)
            ->first();

        if (is_null($user)) {
            $this->form->addError('email', __('auth.failed'));

            return $this->setFailed(__('auth.failed'));
        }

        $correctPassword = Hash::check($this->form->password, $user->password);

        if (! $correctPassword) {
            $this->form->addError('email', __('auth.failed'));

            return $this->setFailed(__('auth.failed'));
        }

        $this->clearLimitAction();

        if (! empty($user->two_factor_secret)) {
            if ($this->form->twoFactorCode === null) {
                $this->twoFactorRequired = true;
                $this->form->twoFactorCode = '';

                return $this->setFailed(__('auth.two-factor-required'));
            }

            $twoFactor = new LoginUsingTwoFactorAction($user, $this->form);
            $twoFactor->handle();

            if ($twoFactor->failed()) {
                $this->form->addError('twoFactorCode', __('auth.two-factor.invalid'));

                return $this->setFailed(__('auth.two-factor.invalid'));
            }
        }

        Auth::login($user, $this->form->remember);

        return $this->setSuccessful();
    }

    public function getTwoFactorRequired(): bool
    {
        return $this->twoFactorRequired;
    }
}
