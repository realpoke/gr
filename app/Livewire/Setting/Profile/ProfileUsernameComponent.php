<?php

namespace App\Livewire\Setting\Profile;

use App\Actions\Setting\Profile\ChangeProfileUsernameAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileUsernameComponent extends Component
{
    public ProfileUsernameForm $form;

    public function mount()
    {
        $this->form->user = $this->user;
        $this->resetForm();
    }

    public function updateUsername()
    {
        $updater = new ChangeProfileUsernameAction($this->form);
        $updater->handle();

        if ($updater->successful()) {
            Flux::toast(__('toast.username-updated'));
        }
    }

    public function resetForm()
    {
        $this->form->username = $this->user->username;
    }

    #[On('echo:Public.Interface,PublicUsernameChangedEvent')]
    public function usernameChanged($event)
    {
        if ($event['user_id'] == $this->user->id) {
            $this->form->username = $event['username'];
        }
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
