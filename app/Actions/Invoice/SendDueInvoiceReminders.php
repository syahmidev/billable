<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Tenant;
use Throwable;

class SendDueInvoiceReminders
{
    public function __construct(private readonly SendInvoiceReminder $reminder) {}

    public function handle(): int
    {
        $sent = 0;

        Tenant::query()->each(function (Tenant $tenant) use (&$sent): void {
            tenancy()->initialize($tenant);

            try {
                Invoice::with('client', 'items')
                    ->whereIn('status', InvoiceStatus::remindableValues())
                    ->whereDate('due_date', '<=', today()->addDays(3))
                    ->where(function ($query): void {
                        $query
                            ->whereNull('last_reminded_at')
                            ->orWhere('last_reminded_at', '<=', now()->subDays(7));
                    })
                    ->whereHas('client', function ($query): void {
                        $query
                            ->whereNotNull('email')
                            ->where('email', '!=', '');
                    })
                    ->each(function (Invoice $invoice) use ($tenant, &$sent): void {
                        try {
                            $this->reminder->handle($invoice, $tenant->name ?? 'billable');
                            $sent++;
                        } catch (Throwable) {
                            // Keep the reminder batch moving if one invoice cannot be mailed.
                        }
                    });
            } finally {
                tenancy()->end();
            }
        });

        return $sent;
    }
}
