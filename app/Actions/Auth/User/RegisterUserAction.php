<?php

namespace App\Actions\Auth\User;

use App\Actions\BaseAction;
use App\Livewire\Auth\Register\RegisterUserForm;
use App\Models\User;
use App\Traits\WithLimits;
use Illuminate\Auth\Events\Registered;

class RegisterUserAction extends BaseAction
{
    use WithLimits;

    private User $user;

    public function __construct(private RegisterUserForm $form) {}

    protected function execute(): self
    {
        $this->limitAction('register', forField: 'form.email');

        $this->form->validate();

        $user = User::create([
            'email' => $this->form->email,
            'username' => $this->form->username,
            'password' => $this->form->password,
        ]);

        event(new Registered($user));

        $this->user = $user;

        return $this->setSuccessful();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
