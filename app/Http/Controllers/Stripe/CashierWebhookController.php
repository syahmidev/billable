<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stripe;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Symfony\Component\HttpFoundation\Response;

class CashierWebhookController extends WebhookController
{
    public function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                $user->update(['plan' => null]);
                Cache::forget('tenant_owner_plan_'.$user->tenant_id);
                Cache::forget('tenant_owner_'.$user->tenant_id);
            }
        }

        return $response;
    }
}
