<?php

namespace App\Actions\Setting\Account;

use App\Actions\Auth\User\LogoutOtherDevicesAction;
use App\Actions\BaseAction;
use App\Livewire\Setting\Account\AccountPasswordForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;

class ChangeAccountPasswordAction extends BaseAction
{
    use WithLimits;

    private bool $loggedOutOthers = false;

    public function __construct(private AccountPasswordForm $form) {}

    public function execute(): self
    {
        $this->limitAction('change-password', forField: 'form.password');

        $this->form->validate();

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
            ]);
        }

        Auth::user()->forceFill([
            'password' => $this->form->password,
        ])->save();

        if ($this->form->logoutOther) {
            $logoutter = new LogoutOtherDevicesAction($this->form->currentPassword);
            $logoutter->handle();

            $this->loggedOutOthers = true;
        }

        $this->form->reset();

        return $this->setSuccessful();
    }

    public function getLoggedOutOthers(): bool
    {
        return $this->loggedOutOthers;
    }
}
