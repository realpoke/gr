<?php

namespace App\Actions\Auth\User;

use App\Actions\BaseAction;
use App\Events\PrivateAccountLogoutSpecificSessionEvent;
use App\Livewire\Setting\Account\AccountLogoutOtherDevicesForm;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogoutOtherSpecificDeviceAction extends BaseAction
{
    use WithLimits;

    public function __construct(private AccountLogoutOtherDevicesForm $form, private string $id) {}

    public function execute(): self
    {
        $this->limitAction('logout-other-specific-device', forField: 'form.password');

        $this->form->validate();

        $updated = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->id)
            ->where('id', $this->id)
            ->update(['invalidated' => true]);

        if ($updated == 0) {
            return $this->setFailed(__('toast.logout-other-specific-device'));
        }
        defer(fn () => broadcast(new PrivateAccountLogoutSpecificSessionEvent($this->id)));

        return $this->setSuccessful();
    }
}
