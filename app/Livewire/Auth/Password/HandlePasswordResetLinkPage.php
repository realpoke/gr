<?php

namespace App\Livewire\Auth\Password;

use App\Actions\Auth\Password\HandlePasswordResetLinkAction;
use Flux\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.reset-password')]
#[Layout('livewire.layouts.auth')]
class HandlePasswordResetLinkPage extends Component
{
    public HandlePasswordResetLinkForm $form;

    #[Locked]
    public string $token;

    #[Locked]
    public string $email;

    public function mount(Request $request, string $token)
    {
        $this->token = $token;
        $this->email = $request->query('email', '');
    }

    public function setNewPassword()
    {
        $resetter = new HandlePasswordResetLinkAction($this->form, $this->token, $this->email);
        $resetter->handle();

        if ($resetter->successful()) {
            Auth::login($resetter->getUser());
            Flux::toast(__('toast.password-reset'));

            return $this->redirect(route('landing.page'), true);
        }
    }
}
