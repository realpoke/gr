<?php

namespace App\Actions\Auth\User;

use App\Actions\BaseAction;
use App\Events\PrivateAccountLogoutEvent;
use App\Livewire\Setting\Account\AccountLogoutOtherDevicesForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LogoutOtherDevicesAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountLogoutOtherDevicesForm $form) {}

    public function execute(): self
    {
        $this->limitAction('logout-other-devices', forField: 'form.password');

        $this->form->validate();

        defer(fn () => broadcast(new PrivateAccountLogoutEvent(Session::getId())));

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->id)
            ->where('id', '!=', Session::getId())
            ->update(['invalidated' => true]);

        return $this->setSuccessful();
    }
}
