<?php

namespace App\Actions\Auth\Email;

use App\Actions\BaseAction;
use App\Events\PrivateAccountEmailVerifiedEvent;
use App\Livewire\Auth\Email\HandleVerificationEmailForm;
use App\Traits\WithLimits;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Validator;

class HandleVerificationEmailAction extends BaseAction
{
    use WithLimits;

    public function __construct(private HandleVerificationEmailForm $form) {}

    public function execute(): self
    {
        $limiter = $this->limitAction('verify-email', throw: false);
        if ($limiter !== null) {
            throw new AuthorizationException;
        }

        $validator = Validator::make($this->form->all(), $this->form->rules());

        if ($validator->fails()) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $this->form->id, (string) Auth::user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $this->form->hash, sha1(Auth::user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if (Auth::user()->hasVerifiedEmail()) {
            return $this->setSuccessful();
        }

        if (Auth::user()->markEmailAsVerified()) {
            Concurrency::defer(function () {
                broadcast(new PrivateAccountEmailVerifiedEvent);
                event(new Verified(Auth::user()));
            });
        }

        return $this->setSuccessful();
    }
}
