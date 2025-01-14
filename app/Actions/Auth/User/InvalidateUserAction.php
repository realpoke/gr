<?php

namespace App\Actions\Auth\User;

use App\Actions\BaseAction;
use Illuminate\Support\Facades\Auth;

class InvalidateUserAction extends BaseAction
{
    public function execute(): self
    {
        Auth::guard('web')->logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return $this->setSuccessful();
    }
}
