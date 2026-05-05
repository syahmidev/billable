<?php

declare(strict_types=1);

namespace App\Enums;

enum StripeEventType: string
{
    case PaymentIntentSucceeded = 'payment_intent.succeeded';
}
