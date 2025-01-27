<?php

namespace App\Livewire\Partials;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class UserInterfaceHandlerComponent extends Component
{
    // TODO: Convert these to js in the frontend
    #[On('echo-private:Interface.{user.id},PrivateAccountLogoutEvent')]
    public function accountLogout($event)
    {
        if ($event['sessionId'] != Session::getId()) {
            Flux::toast(__('toast.force-logout'));

            return $this->redirectIntended(route('authenticate.page'), true);
        }
    }

    #[On('echo-private:Interface.{user.id},PrivateAccountEmailVerifiedEvent')]
    public function accountEmailUpdated()
    {
        Flux::toast(__('toast.email-verified'));
    }

    #[On('echo-private:Interface.{user.id},PrivateUsernameChangedEvent')]
    public function usernameChanged()
    {
        $this->dispatch('private-username-changed')->to(NavigationComponent::class);
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
