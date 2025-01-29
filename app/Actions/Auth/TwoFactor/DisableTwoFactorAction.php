<?php

namespace App\Actions\Auth\TwoFactor;

use App\Actions\BaseAction;
use App\Events\PrivateAccountTwoFactorEvent;
use App\Livewire\Setting\Account\AccountTwoFactorForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

use function Illuminate\Support\defer;

class DisableTwoFactorAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountTwoFactorForm $form) {}

    public function execute(): self
    {
        if (empty(Auth::user()->two_factor_secret)) {
            return $this->setFailed(__('auth.two-factor.already-disabled'));
        }

        $this->limitAction('disable-two-factor', forField: 'form.password');

        $this->form->validate();

        $twoFactor = new Google2FA;

        $validatedKey = $twoFactor->verify($this->form->twoFactorCode, Auth::user()->two_factor_secret);

        if ($validatedKey == false) {
            return $this->setFailed(__('auth.two-factor.invalid'));
        }

        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->save();

        defer(fn () => broadcast(new PrivateAccountTwoFactorEvent));

        return $this->setSuccessful();
    }
}
