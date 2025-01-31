<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('title.profile')]
class ShowProfilePage extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }
}
