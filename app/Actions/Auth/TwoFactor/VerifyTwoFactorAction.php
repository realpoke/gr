<?php

namespace App\Actions\Auth\TwoFactor;

use App\Actions\BaseAction;
use App\Events\PrivateAccountTwoFactorEvent;
use App\Livewire\Setting\Account\AccountTwoFactorForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use PragmaRX\Google2FA\Google2FA;

class VerifyTwoFactorAction extends BaseAction
{
    use WithLimits;

    public function __construct(private string $twoFactorSecret, private AccountTwoFactorForm $form) {}

    public function execute(): self
    {
        if (! empty(Auth::user()->two_factor_secret)) {
            return $this->setFailed(__('auth.two-factor.already-enabled'));
        }

        $this->limitAction('validate-two-factor', forField: 'form.twoFactorCode');

        $this->form->validate();

        $twoFactor = new Google2FA;
        $validKey = $twoFactor->verifyKey($this->twoFactorSecret, $this->form->twoFactorCode);

        if ($validKey == false) {
            return $this->setFailed(__('auth.two-factor.invalid'));
        }

        $user = Auth::user();
        $user->two_factor_secret = $this->twoFactorSecret;
        $user->save();

        Concurrency::defer(fn () => broadcast(new PrivateAccountTwoFactorEvent));

        return $this->setSuccessful();
    }
}
