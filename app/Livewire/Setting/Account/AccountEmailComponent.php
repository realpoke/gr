<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Auth\Email\SendVerificationEmailAction;
use App\Actions\Setting\Account\ChangeAccountEmailAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class AccountEmailComponent extends Component
{
    public AccountEmailForm $form;

    public bool $hasVerifiedEmail;

    #[On('echo-private:Interface.{user.id},PrivateAccountEmailVerifiedEvent')]
    public function boot()
    {
        $this->hasVerifiedEmail = $this->user->hasVerifiedEmail();
    }

    #[On('echo-private:Interface.{user.id},PrivateEmailChangedEvent')]
    public function mount()
    {
        $this->form->user = $this->user;
        $this->resetForm();
    }

    public function updateEmail()
    {
        $updater = new ChangeAccountEmailAction($this->form);
        $updater->handle();

        $this->form->reset('password');

        if ($updater->successful()) {
            Flux::toast(__('toast.email-updated'));
        }

        if ($this->user()->email != $this->form->user->email) {
            $this->hasVerifiedEmail = false;
            Flux::modal('modal.email-verification-needed')->show();
        }
    }

    public function sendEmailVerification()
    {
        $sender = new SendVerificationEmailAction;
        $sender->handle();

        if ($sender->successful()) {
            Flux::toast(__('toast.email-verification-sent'));
            Flux::modal('modal.email-verification-needed')->close();
        }
    }

    public function resetForm()
    {
        $this->form->email = $this->user->email;
        $this->form->reset('password');
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
