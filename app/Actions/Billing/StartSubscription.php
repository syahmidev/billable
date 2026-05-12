<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Cashier\Checkout;

class StartSubscription
{
    public function handle(
        User $user,
        Plan $plan,
        ?string $successUrl = null,
        ?string $cancelUrl = null,
    ): Checkout {
        if (! $plan->isFree() && blank($plan->stripe_price_id)) {
            throw ValidationException::withMessages([
                'plan' => 'This plan is missing a Stripe Price ID. Configure it before starting checkout.',
            ]);
        }

        return $user
            ->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => $successUrl ?? route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl ?? route('plans'),
            ]);
    }
}
