<?php

namespace App\Actions\Setting\Account;

use App\Actions\BaseAction;
use App\Events\PrivateEmailChangedEvent;
use App\Livewire\Setting\Account\AccountEmailForm;
use App\Traits\WithLimits;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Concurrency;

class ChangeAccountEmailAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountEmailForm $form) {}

    public function execute(): self
    {
        $this->limitAction('change-email', forField: 'form.password');

        $this->form->validate();

        if (
            $this->form->email !== $this->form->user->email &&
            $this->form->user instanceof MustVerifyEmail
        ) {
            $this->form->user->forceFill([
                'email' => $this->form->email,
                'email_verified_at' => null,
            ])->save();
        } else {
            $this->form->user->forceFill([
                'email' => $this->form->email,
            ])->save();
        }

        Concurrency::defer(fn () => event(new PrivateEmailChangedEvent($this->form->email)));

        return $this->setSuccessful();
    }
}
