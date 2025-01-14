<?php

namespace App\Actions\Auth\Password;

use App\Actions\BaseAction;
use App\Livewire\Auth\Password\PasswordVerificationNeededForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordVerificationNeededAction extends BaseAction
{
    use WithLimits;

    public function __construct(private PasswordVerificationNeededForm $form) {}

    public function execute(): self
    {
        $this->limitAction('verify-password', forField: 'form.password');

        $this->form->validate();

        if (! Hash::check($this->form->password, Auth::user()->password)) {
            $this->form->addError('password', __('auth.password'));
            $this->form->reset('password');

            return $this->setFailed(__('auth.password'));
        }

        session()->passwordConfirmed();

        return $this->setSuccessful();
    }
}
