<?php

namespace App\Livewire\Partials;

use App\Actions\Auth\User\InvalidateUserAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class UserInterfaceHandlerComponent extends Component
{
    #[On('echo-private:Interface.{user.id},PrivateAccountLogoutEvent')]
    public function accountLogout($event)
    {
        if ($event['sessionId'] != Session::getId()) {
            Flux::toast(__('toast.force-logout'));

            return $this->redirectIntended(route('landing.page'), true);
        }
    }

    #[On('echo-private:Interface.{user.id},PrivateAccountEmailVerifiedEvent')]
    public function accountEmailUpdated()
    {
        Flux::toast(__('toast.email-verified'));
    }

    #[On('echo-private:Interface.{user.id},PrivateAccountLogoutSpecificSessionEvent')]
    public function logoutMe($event)
    {
        if ($event['sessionId'] == Session::getId()) {
            $logoutter = new InvalidateUserAction;
            $logoutter->handle();

            Flux::toast(__('toast.force-logout'));

            return $this->redirectIntended(route('landing.page'), true);
        }
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
