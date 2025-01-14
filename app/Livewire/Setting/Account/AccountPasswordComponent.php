<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Setting\Account\ChangeAccountPasswordAction;
use Flux\Flux;
use Livewire\Component;

class AccountPasswordComponent extends Component
{
    public AccountPasswordForm $form;

    public function updatePassword()
    {
        $updater = new ChangeAccountPasswordAction($this->form);
        $updater->handle();

        if (! $updater->successful()) {
            return;
        }

        if ($updater->getLoggedOutOthers()) {
            Flux::toast(__('toast.password-reset-other-devices'));
        } else {
            Flux::toast(__('toast.password-updated'));
        }
    }

    public function resetForm()
    {
        $this->form->reset();
    }
}
