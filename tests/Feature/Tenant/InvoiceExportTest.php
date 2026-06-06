<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Client;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

class InvoiceExportTest extends TenantTestCase
{
    public function test_authenticated_user_can_export_invoices_as_csv(): void
    {
        [$tenant, $user] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $this->seedSubscription($user);
        $this->createInvoiceInTenant($tenant);

        $response = $this->actingAs($user)
            ->get('http://acme.billable.test/invoices/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString(
            'attachment',
            (string) $response->headers->get('Content-Disposition'),
        );
    }

    public function test_unauthenticated_user_cannot_export_invoices(): void
    {
        $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->get('http://acme.billable.test/invoices/export')
            ->assertRedirect(route('login'));
    }

    public function test_export_csv_contains_invoice_data(): void
    {
        [$tenant, $user] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $this->seedSubscription($user);

        $this->inTenant($tenant, function (): void {
            Client::create(['name' => 'Miyu Corp', 'email' => 'miyu@corp.test'])
                ->invoices()->create([
                    'invoice_number' => 'INV-MIYU',
                    'status' => 'paid',
                    'issue_date' => now()->toDateString(),
                    'due_date' => now()->addDays(30)->toDateString(),
                    'subtotal' => 500.00,
                    'discount_percent' => 0,
                    'tax_percent' => 0,
                    'total' => 500.00,
                    'payment_token' => (string) Str::uuid(),
                ]);
        });

        $response = $this->actingAs($user)
            ->get('http://acme.billable.test/invoices/export');

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('INV-MIYU', $content);
        $this->assertStringContainsString('Miyu Corp', $content);
        $this->assertStringContainsString('paid', $content);
    }

    public function test_export_filters_by_status(): void
    {
        [$tenant, $user] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $this->seedSubscription($user);

        $this->inTenant($tenant, function (): void {
            $client = Client::create(['name' => 'Corp', 'email' => 'corp@test.test']);

            $base = [
                'client_id' => $client->id,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => 100,
                'discount_percent' => 0,
                'tax_percent' => 0,
                'total' => 100,
                'payment_token' => (string) Str::uuid(),
            ];

            $client->invoices()->create(array_merge($base, ['invoice_number' => 'INV-PAID', 'status' => 'paid']));
            $client->invoices()->create(array_merge($base, ['invoice_number' => 'INV-DRAFT', 'status' => 'draft', 'payment_token' => (string) Str::uuid()]));
        });

        $content = $this->actingAs($user)
            ->get('http://acme.billable.test/invoices/export?status=paid')
            ->streamedContent();

        $this->assertStringContainsString('INV-PAID', $content);
        $this->assertStringNotContainsString('INV-DRAFT', $content);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

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
