<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Exceptions\PlanLimitExceededException;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\PlanLimitsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PlanLimitsTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // -------------------------------------------------------------------------
    // Client limits
    // -------------------------------------------------------------------------

    public function test_free_plan_blocks_client_creation_when_limit_reached(): void
    {
        [$tenant, $owner] = $this->createTenantWithUser('acme', 'acme.billable.test');
        // Factory defaults: role = owner, plan = null → PlanLimitsService defaults to 'free'

        $this->inTenant($tenant, function (): void {
            Client::create(['name' => 'A', 'email' => 'a@example.test']);
            Client::create(['name' => 'B', 'email' => 'b@example.test']);
            Client::create(['name' => 'C', 'email' => 'c@example.test']);
        });

        $this->expectException(PlanLimitExceededException::class);

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceClientLimit());
    }

    public function test_free_plan_allows_client_creation_below_limit(): void
    {
        [$tenant] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->inTenant($tenant, function (): void {
            Client::create(['name' => 'A', 'email' => 'a@example.test']);
            Client::create(['name' => 'B', 'email' => 'b@example.test']);
        });

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceClientLimit());

        $this->assertTrue(true);
    }

    public function test_pro_plan_has_no_client_limit(): void
    {
        [$tenant, $owner] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $owner->update(['plan' => 'pro']);

        $this->inTenant($tenant, function (): void {
            for ($i = 1; $i <= 10; $i++) {
                Client::create(['name' => "Client {$i}", 'email' => "client{$i}@example.test"]);
            }
        });

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceClientLimit());

        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // Invoice limits
    // -------------------------------------------------------------------------

    public function test_free_plan_blocks_invoice_creation_when_monthly_limit_reached(): void
    {
        [$tenant] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->inTenant($tenant, function (): void {
            $client = Client::create(['name' => 'Client', 'email' => 'client@example.test']);

            for ($i = 1; $i <= 5; $i++) {
                Invoice::create($this->invoiceAttrs($client->id));
            }
        });

        $this->expectException(PlanLimitExceededException::class);

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceInvoiceLimit());
    }

    public function test_free_plan_allows_invoice_creation_below_monthly_limit(): void
    {
        [$tenant] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->inTenant($tenant, function (): void {
            $client = Client::create(['name' => 'Client', 'email' => 'client@example.test']);

            for ($i = 1; $i <= 4; $i++) {
                Invoice::create($this->invoiceAttrs($client->id));
            }
        });

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceInvoiceLimit());

        $this->assertTrue(true);
    }

    public function test_invoices_from_previous_month_do_not_count_toward_current_month_limit(): void
    {
        [$tenant] = $this->createTenantWithUser('acme', 'acme.billable.test');

        $this->inTenant($tenant, function (): void {
            $client = Client::create(['name' => 'Client', 'email' => 'client@example.test']);

            // 5 invoices — created, then backdated to last month via query builder
            for ($i = 1; $i <= 5; $i++) {
                $invoice = Invoice::create($this->invoiceAttrs($client->id));
                Invoice::where('id', $invoice->id)->update(['created_at' => now()->subMonth()]);
            }
        });

        // Current month has 0 invoices — should be under limit
        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceInvoiceLimit());

        $this->assertTrue(true);
    }

    public function test_pro_plan_has_no_invoice_limit(): void
    {
        [$tenant, $owner] = $this->createTenantWithUser('acme', 'acme.billable.test');
        $owner->update(['plan' => 'pro']);

        $this->inTenant($tenant, function (): void {
            $client = Client::create(['name' => 'Client', 'email' => 'client@example.test']);

            for ($i = 1; $i <= 10; $i++) {
                Invoice::create($this->invoiceAttrs($client->id));
            }
        });

        $this->inTenant($tenant, fn () => app(PlanLimitsService::class)->enforceInvoiceLimit());

        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function invoiceAttrs(int $clientId): array
    {
        return [
            'invoice_number' => 'INV-'.Str::upper(Str::random(6)),
            'client_id' => $clientId,
            'status' => 'draft',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 100.00,
            'discount_percent' => 0,
            'tax_percent' => 0,
            'total' => 100.00,
            'payment_token' => (string) Str::uuid(),
        ];
    }
}
