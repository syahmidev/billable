<?php

declare(strict_types=1);

use App\Actions\Invoice\MarkOverdueInvoices;
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

Artisan::command('invoices:mark-overdue', function (MarkOverdueInvoices $action): void {
    $marked = $action->handle();

    $this->info("Marked {$marked} invoice".($marked === 1 ? '' : 's').' as overdue.');
})->purpose('Transition sent invoices past their due date to overdue status');

Schedule::command('invoices:mark-overdue')->dailyAt('00:01')->withoutOverlapping();
Schedule::command('invoices:send-reminders')->dailyAt('09:00')->withoutOverlapping();
