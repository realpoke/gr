<?php

namespace App\Livewire\Setting\Account;

use App\Actions\Auth\User\LogoutOtherDevicesAction;
use App\Actions\Auth\User\LogoutOtherSpecificDeviceAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:Interface.{user.id},PrivateAccountLogoutEvent')]
#[On('echo-private:Interface.{user.id},PrivateAccountLogoutSpecificSessionEvent')]
class AccountLogoutOtherDevicesComponent extends Component
{
    public AccountLogoutOtherDevicesForm $form;

    public function logoutOtherDevices()
    {
        $logoutter = new LogoutOtherDevicesAction($this->form);
        $logoutter->handle();

        $this->form->reset('password');

        if (! $logoutter->successful()) {
            return;
        }

        Flux::toast(__('toast.logout-other-devices'));
    }

    public function logoutSpecificDevice(string $id)
    {
        $logoutter = new LogoutOtherSpecificDeviceAction($this->form, $id);
        $logoutter->handle();

        if (! $logoutter->successful()) {
            return;
        }

        Flux::toast(__('toast.logout-other-specific-device'));
    }

    #[Computed()]
    public function sessions(): Collection
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $this->user->id)
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'agent' => $this->createAgent($session),
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'id' => $session->id,
                'logged_out' => $session->invalidated,
            ];
        });
    }

    private function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function resetForm()
    {
        $this->form->reset();
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
