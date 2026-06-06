<?php

declare(strict_types=1);

use App\Actions\Invoice\MarkOverdueInvoices;
use App\Actions\Invoice\SendDueInvoiceReminders;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('invoices:send-reminders', function (SendDueInvoiceReminders $reminders): void {
    $sent = $reminders->handle();

    $this->info("Sent {$sent} invoice reminder".($sent === 1 ? '' : 's').'.');
})->purpose('Send invoice reminders for due and overdue invoices');

Artisan::command('invoices:mark-overdue', function (MarkOverdueInvoices $action): void {
    $marked = $action->handle();

    $this->info("Marked {$marked} invoice".($marked === 1 ? '' : 's').' as overdue.');
})->purpose('Transition sent invoices past their due date to overdue status');

Schedule::command('invoices:mark-overdue')->dailyAt('00:01')->withoutOverlapping();
Schedule::command('invoices:send-reminders')->dailyAt('09:00')->withoutOverlapping();

Artisan::command('invoices:backfill-tokens', function (): void {
    $filled = 0;

    Tenant::query()->each(function (Tenant $tenant) use (&$filled): void {
        tenancy()->initialize($tenant);

        try {
            Invoice::whereNull('payment_token')->each(function (Invoice $invoice) use (&$filled): void {
                $invoice->update(['payment_token' => (string) Str::uuid()]);
                $filled++;
            });
        } finally {
            tenancy()->end();
        }
    });

    $this->info("Backfilled {$filled} invoice token".($filled === 1 ? '' : 's').'.');
})->purpose('Backfill missing payment tokens on invoices created before the payment_token column was added');
