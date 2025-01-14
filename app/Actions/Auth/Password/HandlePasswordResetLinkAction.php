<?php

namespace App\Actions\Auth\Password;

use App\Actions\BaseAction;
use App\Livewire\Auth\Password\HandlePasswordResetLinkForm;
use App\Models\User;
use App\Traits\WithLimits;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class HandlePasswordResetLinkAction extends BaseAction
{
    use WithLimits;

    private User $user;

    public function __construct(
        private HandlePasswordResetLinkForm $form,
        private string $token,
        private string $email
    ) {}

    public function execute(): self
    {
        $this->limitAction('reset-password', forField: 'form.password');

        $this->form->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->form->password,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->password = $password;
                $user->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
                $this->user = $user;
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->form->addError('password', __($status));

            return $this->setFailed(__($status));
        }

        return $this->setSuccessful();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
