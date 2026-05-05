<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class TenantTestCase extends TestCase
{
    // DatabaseMigrations runs migrate:fresh outside a transaction so that
    // PostgreSQL's CREATE DATABASE (fired on Tenant::create) is allowed.
    use DatabaseMigrations;

    /** @var list<Tenant> */
    private array $createdTenants = [];

    /**
     * Create a tenant with its database and a member user.
     * Returns [$tenant, $user].
     */
    protected function createTenantWithUser(string $id, string $domain): array
    {
        $tenant = Tenant::create(['id' => $id, 'name' => ucfirst($id)]);
        $tenant->domains()->create(['domain' => $domain]);

        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->createdTenants[] = $tenant;

        return [$tenant, $user];
    }

    /**
     * Run a callback inside a tenant's database context, then return to central.
     */
    protected function inTenant(Tenant $tenant, callable $callback): mixed
    {
        tenancy()->initialize($tenant);
        try {
            return $callback();
        } finally {
            tenancy()->end();
        }
    }

    /**
     * Seed a client into the given tenant's database.
     */
    protected function createClientInTenant(Tenant $tenant, array $attrs = []): Client
    {
        return $this->inTenant($tenant, fn () => Client::create(array_merge([
            'name' => 'Test Client',
            'email' => 'client@example.test',
        ], $attrs)));
    }

    /**
     * Seed an invoice (with its client) into the given tenant's database.
     */
    protected function createInvoiceInTenant(Tenant $tenant, array $attrs = []): Invoice
    {
        return $this->inTenant($tenant, function () use ($attrs) {
            $client = Client::create(['name' => 'Test Client', 'email' => 'client@example.test']);

            return Invoice::create(array_merge([
                'invoice_number' => 'INV-'.Str::upper(Str::random(4)),
                'client_id' => $client->id,
                'status' => 'draft',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => 100.00,
                'discount_percent' => 0,
                'tax_percent' => 0,
                'total' => 100.00,
                'payment_token' => (string) Str::uuid(),
            ], $attrs));
        });
    }

    protected function tearDown(): void
    {
        tenancy()->end();

        // Drop each tenant's PostgreSQL database before the test transaction rolls back.
        // We use the stancl/tenancy database manager directly so it connects to
        // the postgres maintenance DB and issues DROP DATABASE from there.
        foreach ($this->createdTenants as $tenant) {
            try {
                $tenant->database()->manager()->deleteDatabase($tenant);
            } catch (\Throwable) {
                // Ignore — database may not have been created if the test failed early.
            }
        }

        $this->createdTenants = [];

        parent::tearDown();
    }
}
