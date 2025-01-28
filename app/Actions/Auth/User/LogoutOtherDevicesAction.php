<?php

namespace App\Actions\Auth\User;

use App\Actions\BaseAction;
use App\Events\PrivateAccountLogoutEvent;
use App\Livewire\Setting\Account\AccountDeleteForm;
use App\Livewire\Setting\Account\AccountLogoutOtherDevicesForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LogoutOtherDevicesAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountLogoutOtherDevicesForm|AccountDeleteForm $form) {}

    public function execute(): self
    {
        $this->limitAction('logout-other-devices', forField: 'form.password');

        $this->form->validate();

        Auth::logoutOtherDevices($this->form->password);

        defer(function () {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->id)
                ->where('id', '!=', Session::getId())
                ->delete();
            broadcast(new PrivateAccountLogoutEvent(Session::getId()));
        });

        return $this->setSuccessful();
    }
}
