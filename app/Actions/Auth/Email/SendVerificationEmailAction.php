<?php

namespace App\Actions\Auth\Email;

use App\Actions\BaseAction;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;

use function Illuminate\Support\defer;

class SendVerificationEmailAction extends BaseAction
{
    use WithLimits;

    public function execute(): self
    {
        $this->limitAction('send-verification-email', 1);

        if (Auth::user()->hasVerifiedEmail()) {
            return $this->setSuccessful();
        }

        defer(fn () => Auth::user()->sendEmailVerificationNotification());

        return $this->setSuccessful();
    }
}
