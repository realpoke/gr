<?php

namespace App\Livewire\Auth\Password;

use App\Actions\Auth\Password\PasswordVerificationNeededAction;
use App\Actions\Auth\User\InvalidateUserAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.password-verification')]
#[Layout('livewire.layouts.auth')]
class PasswordVerificationNeededPage extends Component
{
    public PasswordVerificationNeededForm $form;

    public function checkPassword()
    {
        $validator = new PasswordVerificationNeededAction($this->form);
        $validator->handle();

        if ($validator->successful()) {
            Flux::toast(__('toast.password-verified'));

            return $this->redirectIntended(route('landing.page'), true);
        }
    }

    public function logout()
    {
        $invalidator = new InvalidateUserAction;
        $invalidator->handle();

        if ($invalidator->successful()) {
            return $this->redirect(route('landing.page'), true);
        }
    }
}
