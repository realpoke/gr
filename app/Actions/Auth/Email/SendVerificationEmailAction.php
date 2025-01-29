<?php

namespace App\Actions\Auth\Email;

use App\Actions\BaseAction;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Concurrency;

class SendVerificationEmailAction extends BaseAction
{
    use WithLimits;

    public function execute(): self
    {
        $this->limitAction('send-verification-email', 1);

        if (Auth::user()->hasVerifiedEmail()) {
            return $this->setSuccessful();
        }

        Concurrency::defer(fn () => Auth::user()->sendEmailVerificationNotification());

        return $this->setSuccessful();
    }
}
