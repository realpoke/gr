<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MeProfilePage extends Component
{
    public function boot()
    {
        return $this->redirect(Auth::user()->page(), true);
    }

    public function render()
    {
        return view('livewire.profile.show-profile-page');
    }
}
