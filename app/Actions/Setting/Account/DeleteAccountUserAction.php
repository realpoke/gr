<?php

namespace App\Actions\Setting\Account;

use App\Actions\Auth\User\LogoutOtherDevicesAction;
use App\Actions\BaseAction;
use App\Livewire\Setting\Account\AccountDeleteForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeleteAccountUserAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountDeleteForm $form) {}

    public function execute(): self
    {
        $this->limitAction('delete-account', forField: 'form.password');

        try {
            $this->form->validate();
        } catch (ValidationException $e) {
            $this->form->password = '';
            throw $e;
        }

        $logoutter = new LogoutOtherDevicesAction($this->form);
        $logoutter->handle();

        defer(function () {
            Auth::user()->delete();

            if (request()->hasSession()) {
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }
        });

        return $this->setSuccessful();
    }
}
