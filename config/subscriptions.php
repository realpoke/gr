<?php

return [
    'plans' => [
        'monthly' => [
            'price_id' => env('SUBSCRIPTION_MONTHLY_PRICE_ID'),
        ],
        'annualy' => [
            'price_id' => env('SUBSCRIPTION_ANNUALY_PRICE_ID'),
        ],
    ],
];
