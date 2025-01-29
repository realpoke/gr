<?php

namespace App\Actions\Setting\Profile;

use App\Actions\BaseAction;
use App\Events\PrivateUsernameChangedEvent;
use App\Events\PublicUsernameChangedEvent;
use App\Livewire\Setting\Profile\ProfileUsernameForm;
use App\Traits\WithLimits;

use function Illuminate\Support\defer;

class ChangeProfileUsernameAction extends BaseAction
{
    use WithLimits;

    public function __construct(private ProfileUsernameForm $form) {}

    public function execute(): self
    {
        $this->limitAction('change-username', forField: 'form.username');

        $this->form->validate();

        $this->form->user->forceFill([
            'username' => $this->form->username,
        ])->save();

        $user = $this->form->user;

        defer(function () use ($user) {
            broadcast(new PublicUsernameChangedEvent($user));
            broadcast(new PrivateUsernameChangedEvent);
        });

        return $this->setSuccessful();
    }
}
