<?php

namespace App\Livewire\Auth\Authenticate;

use App\Actions\Auth\User\AuthenticateUserAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.login')]
#[Layout('livewire.layouts.auth')]
class AuthenticateUserPage extends Component
{
    public AuthenticateUserForm $form;

    public function authenticate()
    {
        $authenticate = new AuthenticateUserAction($this->form);
        $authenticate->handle();

        if ($authenticate->successful()) {
            Flux::modals()->close();

            return $this->redirectIntended(route('landing.page'), true);
        }

        if ($authenticate->getTwoFactorRequired()) {
            $this->form->resetErrorBag();
            Flux::modal('two-factor-modal')->show();

            return;
        }
    }
}
