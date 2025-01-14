<?php

namespace App\Livewire\Auth\Email;

use App\Actions\Auth\Email\HandleVerificationEmailAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.verifying')]
#[Layout('livewire.layouts.auth')]
class HandleVerificationEmailPage extends Component
{
    public bool $verified = false;

    public HandleVerificationEmailForm $form;

    public function mount(string $id, string $hash)
    {
        $this->form->id = $id;
        $this->form->hash = $hash;

        $verification = new HandleVerificationEmailAction($this->form);
        $verification->handle();

        if ($verification->successful()) {
            Flux::toast(__('toast.email-verified'));
        }

        return $this->redirectIntended(route('setting.page'), true);
    }
}
