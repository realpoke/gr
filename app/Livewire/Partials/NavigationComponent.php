<?php

namespace App\Livewire\Partials;

use App\Actions\Auth\User\InvalidateUserAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('username-changed')]
class NavigationComponent extends Component
{
    public function logout()
    {
        $logoutter = new InvalidateUserAction;
        $logoutter->handle();

        if ($logoutter->successful()) {
            Flux::toast(__('toast.logout-success'));

            return $this->redirect(route('landing.page'), true);
        }
    }

    #[Computed()]
    public function user(): ?User
    {
        return Auth::user();
    }
}
