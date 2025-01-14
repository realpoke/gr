<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Setting\Account\DeleteAccountUserAction;
use Livewire\Component;

class AccountDeleteComponent extends Component
{
    public AccountDeleteForm $form;

    public function deleteAccount()
    {
        $delete = new DeleteAccountUserAction($this->form);
        $delete->handle();
    }
}
