<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Canceled = 'canceled';
    case Incomplete = 'incomplete';
    case IncompleteExpired = 'incomplete_expired';
    case PastDue = 'past_due';
    case Paused = 'paused';
    case Trialing = 'trialing';
    case Unpaid = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Canceled => 'Canceled',
            self::Incomplete => 'Payment incomplete',
            self::IncompleteExpired => 'Checkout expired',
            self::PastDue => 'Past due',
            self::Paused => 'Paused',
            self::Trialing => 'Trialing',
            self::Unpaid => 'Unpaid',
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::Active => 'Stripe subscription is active for this workspace.',
            self::Canceled => 'This subscription has been canceled. Choose a plan to restore access.',
            self::Incomplete => 'Stripe still needs a completed payment method before the subscription becomes active.',
            self::IncompleteExpired => 'The checkout session expired before payment completed.',
            self::PastDue => 'The latest subscription payment failed. Update the payment method in Stripe.',
            self::Paused => 'The subscription is paused. Resume it in Stripe to keep access active.',
            self::Trialing => 'This workspace is currently in a Stripe trial.',
            self::Unpaid => 'Stripe marked this subscription unpaid. Update billing details to recover access.',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Active, self::Trialing => 'success',
            self::Incomplete, self::PastDue, self::Paused, self::Unpaid => 'warning',
            self::Canceled, self::IncompleteExpired => 'danger',
        };
    }

    public function needsAttention(): bool
    {
        return in_array($this, [
            self::Canceled,
            self::Incomplete,
            self::IncompleteExpired,
            self::PastDue,
            self::Paused,
            self::Unpaid,
        ], true);
    }
}
