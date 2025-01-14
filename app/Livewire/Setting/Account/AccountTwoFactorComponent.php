<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Auth\TwoFactor\DisableTwoFactorAction;
use App\Actions\Auth\TwoFactor\EnableTwoFactorAction;
use App\Actions\Auth\TwoFactor\VerifyTwoFactorAction;
use App\Actions\Auth\User\LogoutOtherDevicesAction;
use App\Models\User;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:Interface.{user.id},PrivateAccountTwoFactorEvent')]
class AccountTwoFactorComponent extends Component
{
    public string $twoFactorSecret = '';

    public ?string $qrSvg = null;

    public AccountTwoFactorForm $form;

    public function enableTwoFactor()
    {
        $twoFactor = new EnableTwoFactorAction($this->form);
        $twoFactor->handle();

        if ($twoFactor->failed()) {
            $this->form->addError('password', $twoFactor->getErrorMessage());

            return;
        }

        $this->qrSvg = $this->twoFactorQrCodeSvg($twoFactor->getQrCode());
        $this->twoFactorSecret = $twoFactor->getSecretKey();

        $this->form->resetErrorBag();
        Flux::modal('two-factor-modal')->show();
    }

    public function disableTwoFactor()
    {
        $twoFactor = new DisableTwoFactorAction($this->form);
        $twoFactor->handle();

        if ($twoFactor->failed()) {
            $this->addError('form.twoFactorCode', $twoFactor->getErrorMessage());

            return;
        }

        Flux::toast(__('toast.two-factor-disabled'));

        $this->resetForm();
    }

    public function verifyTwoFactor()
    {
        $twoFactor = new VerifyTwoFactorAction($this->twoFactorSecret, $this->form);
        $twoFactor->handle();

        if ($twoFactor->failed()) {
            $this->addError('form.twoFactorCode', $twoFactor->getErrorMessage());

            return;
        }

        Flux::modal('two-factor-modal')->close();

        if ($this->form->logoutOther) {
            $logoutter = new LogoutOtherDevicesAction($this->form->password);
            $logoutter->handle();

            if ($logoutter->failed()) {
                Flux::toast(__('toast.logout-other-specific-device-failed'));
            }

            Flux::toast(__('toast.two-factor-logout-other-devices'));
        } else {
            Flux::toast(__('toast.two-factor-enabled'));
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form->reset();
        $this->reset();
    }

    private function twoFactorQrCodeSvg(string $qrCodeUrl)
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(334, 2, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))),
                new SvgImageBackEnd
            )
        ))->writeString($qrCodeUrl);

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    #[Computed()]
    public function hasTwoFactor(): bool
    {
        return ! empty(Auth::user()->two_factor_secret);
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
