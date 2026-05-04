<?php

namespace App\Actions\Billing;

use App\Models\Plan;
use App\Models\User;
use Laravel\Cashier\Checkout;

class StartSubscription
{
    public function handle(User $user, Plan $plan): Checkout
    {
        return $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('plans'),
            ]);
    }
}
