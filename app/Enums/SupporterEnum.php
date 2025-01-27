<?php

namespace App\Enums;

enum SupporterEnum: string
{
    case MONTHLY = 'monthly';
    case ANNUALY = 'annualy';

    public function priceId(): string
    {
        return match ($this) {
            self::MONTHLY => config('subscriptions.plans.monthly.price_id'),
            self::ANNUALY => config('subscriptions.plans.annualy.price_id'),
        };
    }
}
