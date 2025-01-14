<?php

namespace App\Actions\Auth\Password;

use App\Actions\BaseAction;
use App\Livewire\Auth\Password\SendPasswordResetLinkForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction extends BaseAction
{
    use WithLimits;

    public function __construct(private SendPasswordResetLinkForm $form) {}

    public function execute(): self
    {
        $this->limitAction('send-password-reset-link', forField: 'form.email');

        $this->form->validate();

        $status = Password::sendResetLink(['email' => $this->form->email]);

        if ($status != Password::RESET_LINK_SENT) {
            $this->form->addError('email', __($status));

            return $this->setFailed($status);
        }

        $this->form->reset('email');
        $this->clearLimitAction();

        $this->limitAction('send-password-reset-link', 1, forField: 'form.email');

        return $this->setSuccessful();
    }
}
