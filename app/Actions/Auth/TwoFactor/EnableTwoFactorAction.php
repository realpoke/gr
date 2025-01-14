<?php

namespace App\Actions\Auth\TwoFactor;

use App\Actions\BaseAction;
use App\Livewire\Setting\Account\AccountTwoFactorForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class EnableTwoFactorAction extends BaseAction
{
    use WithLimits;

    private ?string $secretKey = null;

    private ?string $qrCode = null;

    public function __construct(private AccountTwoFactorForm $form) {}

    public function execute(): self
    {
        if (! empty(Auth::user()->two_factor_secret)) {
            return $this->setFailed(__('auth.two-factor.already-enabled'));
        }

        $this->limitAction('enable-two-factor', forField: 'form.password');

        $this->form->validateOnly('password');

        $twoFactor = new Google2FA;

        $this->secretKey = $twoFactor->generateSecretKey(64);
        $this->qrCode = $twoFactor->getQRCodeUrl(
            config('app.name'),
            Auth::user()->email,
            $this->secretKey,
        );

        return $this->setSuccessful();
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }
}
