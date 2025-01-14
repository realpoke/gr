<?php

namespace App\Livewire\Auth\Password;

use App\Actions\Auth\Password\SendPasswordResetLinkAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.reset-password')]
#[Layout('livewire.layouts.auth')]
class SendPasswordResetLinkPage extends Component
{
    public SendPasswordResetLinkForm $form;

    public function sendPasswordResetLink()
    {
        $sender = new SendPasswordResetLinkAction($this->form);
        $sender->handle();

        if ($sender->successful()) {
            Flux::toast(__('toast.password-reset-link-sent'));
        }
    }
}
