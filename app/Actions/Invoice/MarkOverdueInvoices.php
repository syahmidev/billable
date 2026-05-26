<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Tenant;
use Throwable;

class MarkOverdueInvoices
{
    public function handle(): int
    {
        $marked = 0;

        Tenant::query()->each(function (Tenant $tenant) use (&$marked): void {
            tenancy()->initialize($tenant);

            try {
                $count = Invoice::where('status', InvoiceStatus::Sent->value)
                    ->whereDate('due_date', '<', today())
                    ->update(['status' => InvoiceStatus::Overdue->value]);

                $marked += $count;
            } catch (Throwable) {
                // Keep processing remaining tenants if one tenant fails.
            } finally {
                tenancy()->end();
            }
        });

        return $marked;
    }
}
