<?php

namespace App\Livewire\Auth\Email;

use App\Actions\Auth\Email\SendVerificationEmailAction;
use App\Actions\Auth\User\InvalidateUserAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.email-verification')]
#[Layout('livewire.layouts.auth')]
class EmailVerificationNeededPage extends Component
{
    public function resend()
    {
        $sender = new SendVerificationEmailAction;
        $sender = $sender->handle();

        if ($sender->successful()) {
            Flux::toast(__('toast.email-verification-sent'));
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
