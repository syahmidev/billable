<?php

declare(strict_types=1);

return [
    'stripe_webhooks' => [
        'invoice_secret' => env('STRIPE_INVOICE_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET')),
    ],

    'stripe_prices' => [
        'pro' => env('STRIPE_PRICE_PRO'),
        'business' => env('STRIPE_PRICE_BUSINESS'),
    ],
];
