<?php

namespace App\Actions\Setting\Billing;

use App\Actions\BaseAction;
use App\Models\User;
use Laravel\Paddle\Cashier;

class GetBillingPortalAction extends BaseAction
{
    private string $portalUrl;

    public function getPortalUrl(): string
    {
        return $this->portalUrl;
    }

    public function __construct(private User $user) {}

    public function execute(): self
    {
        $customer = $this->user->customer()->first();

        if (! $customer) {
            return $this->setFailed('Could not find customer.');
        }

        $response = Cashier::api(
            method: 'POST',
            uri: 'customers/'.$customer->paddle_id.'/portal-sessions'
        );

        if (! $response->successful()) {
            return $this->setFailed('Failed to get billing portal url.');
        }

        $this->portalUrl = $response['data']['urls']['general']['overview'];

        return $this->setSuccessful();
    }
}
