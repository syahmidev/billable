<?php

declare(strict_types=1);

use App\Actions\Invoice\SendDueInvoiceReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('invoices:send-reminders', function (SendDueInvoiceReminders $reminders): void {
    $sent = $reminders->handle();

    $this->info("Sent {$sent} invoice reminder".($sent === 1 ? '' : 's').'.');
})->purpose('Send invoice reminders for due and overdue invoices');

Schedule::command('invoices:send-reminders')->dailyAt('09:00');
