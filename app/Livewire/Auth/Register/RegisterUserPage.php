<?php

namespace App\Livewire\Auth\Register;

use App\Actions\Auth\User\RegisterUserAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.register')]
#[Layout('livewire.layouts.auth')]
class RegisterUserPage extends Component
{
    // FIXME: When creating an account it don't allow scrolling, might have a modal open in the background
    public RegisterUserForm $form;

    public function register()
    {
        $register = new RegisterUserAction($this->form);
        $register->handle();

        if ($register->successful()) {
            Auth::login($register->getUser());

            return $this->redirectIntended(route('landing.page'), true);
        }
    }
}
