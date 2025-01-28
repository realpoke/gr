<?php

namespace App\Livewire\Setting;

use App\Actions\Setting\Billing\GetBillingPortalAction;
use App\Models\User;
use Flux\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('title.setting')]
class SettingPage extends Component
{
    #[Url('tab', except: 'account')]
    public string $tab = 'account';

    private static array $tabs = [
        'account',
        'profile',
        'supporter',
        'billing',
    ];

    public function mount(Request $request)
    {
        $incomingTab = $this->getValidTab($request);

        if (! is_null($incomingTab)) {
            $this->tab = $incomingTab;
        } else {
            $this->reset('tab');
        }
    }

    private function getValidTab(Request $request): ?string
    {
        $incomingTab = $request->input('tab', null);
        if (! is_null($incomingTab) && in_array($incomingTab, $this::$tabs)) {
            return $incomingTab;
        }

        return null;
    }

    public function billingPortal()
    {
        if (! $this->user->isCustomer()) {
            Flux::toast(__('toast.billing-portal-failed'));
        }

        $redirect = new GetBillingPortalAction($this->user);
        $redirect->handle();

        if ($redirect->failed()) {
            Flux::toast(__('toast.billing-portal-failed'));

            return;
        }

        Flux::toast(__('toast.billing-portal-success'));

        $this->redirect($redirect->getPortalUrl());
    }

    #[Computed()]
    public function user(): User
    {
        return Auth::user();
    }
}
