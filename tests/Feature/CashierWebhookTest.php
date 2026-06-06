<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Controllers\Stripe\CashierWebhookController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CashierWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_cancelled_resets_user_plan_to_null(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_abc123',
            'plan' => 'pro',
        ]);

        app(CashierWebhookController::class)->handleCustomerSubscriptionDeleted(
            $this->payload('cus_test_abc123', 'sub_test_001'),
        );

        $this->assertNull($user->fresh()->plan);
    }

    public function test_subscription_cancelled_with_unknown_customer_is_a_noop(): void
    {
        app(CashierWebhookController::class)->handleCustomerSubscriptionDeleted(
            $this->payload('cus_does_not_exist', 'sub_test_002'),
        );

        // No exception thrown — unknown customer is silently ignored
        $this->assertTrue(true);
    }

    public function test_subscription_cancelled_clears_plan_limit_cache(): void
    {
        $user = User::factory()->create([
            'stripe_id' => 'cus_test_def456',
            'plan' => 'business',
            'tenant_id' => 'acme',
        ]);

        Cache::put('tenant_owner_plan_acme', 'business');
        Cache::put('tenant_owner_acme', $user->id);

        app(CashierWebhookController::class)->handleCustomerSubscriptionDeleted(
            $this->payload('cus_test_def456', 'sub_test_003'),
        );

        $this->assertNull(Cache::get('tenant_owner_plan_acme'));
        $this->assertNull(Cache::get('tenant_owner_acme'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function payload(string $customerId, string $subscriptionId): array
    {
        return [
            'data' => [
                'object' => [
                    'id' => $subscriptionId,
                    'object' => 'subscription',
                    'customer' => $customerId,
                    'status' => 'canceled',
                ],
            ],
        ];
    }
}
