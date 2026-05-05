<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

class TenantIsolationTest extends TenantTestCase
{
    // -------------------------------------------------------------------------
    // Scenario 1 — User cannot access another tenant's client
    // -------------------------------------------------------------------------

    public function test_user_cannot_access_another_tenants_client(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('acme', 'acme.billable.test');
        [$tenantB] = $this->createTenantWithUser('globex', 'globex.billable.test');

        $clientB = $this->createClientInTenant($tenantB, ['name' => 'Globex Client']);

        // User A hits tenant B's subdomain → EnsureTenantMember returns 403
        $this->actingAs($userA)
            ->get("http://globex.billable.test/clients/{$clientB->id}")
            ->assertForbidden();
    }

    public function test_user_can_access_their_own_tenants_client(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $this->seedSubscription($userA);

        $clientA = $this->createClientInTenant($tenantA, ['name' => 'Acme Client']);

        $this->actingAs($userA)
            ->get("http://acme.billable.test/clients/{$clientA->id}")
            ->assertOk();
    }

    // -------------------------------------------------------------------------
    // Scenario 2 — User cannot access another tenant's invoice
    // -------------------------------------------------------------------------

    public function test_user_cannot_access_another_tenants_invoice(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('acme', 'acme.billable.test');
        [$tenantB] = $this->createTenantWithUser('globex', 'globex.billable.test');

        $invoiceB = $this->createInvoiceInTenant($tenantB);

        $this->actingAs($userA)
            ->get("http://globex.billable.test/invoices/{$invoiceB->id}")
            ->assertForbidden();
    }

    public function test_user_can_access_their_own_tenants_invoice(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $this->seedSubscription($userA);

        $invoiceA = $this->createInvoiceInTenant($tenantA);

        $this->actingAs($userA)
            ->get("http://acme.billable.test/invoices/{$invoiceA->id}")
            ->assertOk();
    }

    // -------------------------------------------------------------------------
    // Scenario 3 — Public payment token only resolves inside the correct tenant
    // -------------------------------------------------------------------------

    public function test_payment_token_resolves_in_correct_tenant(): void
    {
        [$tenantA] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $token = (string) Str::uuid();
        $this->createInvoiceInTenant($tenantA, ['payment_token' => $token, 'status' => 'sent']);

        $this->get("http://acme.billable.test/pay/{$token}")
            ->assertOk();
    }

    public function test_payment_token_does_not_resolve_in_wrong_tenant(): void
    {
        [$tenantA] = $this->createTenantWithUser('acme', 'acme.billable.test');
        [$tenantB] = $this->createTenantWithUser('globex', 'globex.billable.test');

        // Create the invoice in tenant A's database
        $token = (string) Str::uuid();
        $this->createInvoiceInTenant($tenantA, ['payment_token' => $token, 'status' => 'sent']);

        // The same token against tenant B's subdomain → 404 (not in that DB)
        $this->get("http://globex.billable.test/pay/{$token}")
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // Scenario 4 — Central routes do not expose tenant data
    // -------------------------------------------------------------------------

    public function test_central_routes_do_not_expose_tenant_data(): void
    {
        [$tenantA, $userA] = $this->createTenantWithUser('acme', 'acme.billable.test');

        // Seed a client in tenant A's private database
        $this->createClientInTenant($tenantA, ['name' => 'Top Secret Corp']);

        // Central landing page — no tenant context, no DB switch
        $this->get('http://billable.test/')
            ->assertOk()
            ->assertDontSee('Top Secret Corp');
    }

    public function test_central_auth_routes_are_accessible_without_tenant_context(): void
    {
        $this->get('http://billable.test/login')->assertOk();
        $this->get('http://billable.test/register')->assertOk();
    }

    public function test_unauthenticated_user_cannot_reach_tenant_dashboard(): void
    {
        [$tenantA] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->get('http://acme.billable.test/dashboard')
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Give a user an active plan so EnsureSubscribed passes.
     */
    private function seedSubscription(User $user): void
    {
        $plan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'stripe_price_id' => 'price_test_pro',
                'price' => 29,
                'features' => ['Unlimited clients'],
                'is_active' => true,
            ]
        );

        $user->update(['plan' => $plan->slug]);

        // Seed a Cashier subscription row so EnsureSubscribed is satisfied
        $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.Str::random(8),
            'stripe_status' => 'active',
            'stripe_price' => $plan->stripe_price_id,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);
    }
}
