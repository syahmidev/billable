<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Jobs\Invoice\SendInvoiceReminderEmail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SendInvoiceReminder
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, string $workspaceName, ?User $actor = null): void
    {
        $invoice->loadMissing('client', 'items');

        if (! in_array($invoice->status, InvoiceStatus::remindableValues(), true)) {
            throw ValidationException::withMessages([
                'invoice' => 'Only sent or overdue invoices can receive reminders.',
            ]);
        }

        if (! $invoice->client?->email) {
            throw ValidationException::withMessages([
                'invoice' => 'Add an email address to the client before sending a reminder.',
            ]);
        }

        $status = $invoice->due_date->lt(today())
            ? InvoiceStatus::Overdue->value
            : $invoice->status;

        $invoice->forceFill([
            'status' => $status,
            'reminders_sent' => ((int) $invoice->reminders_sent) + 1,
            'last_reminded_at' => now(),
        ])->save();

        SendInvoiceReminderEmail::dispatch($invoice->id, $invoice->client->email, $workspaceName)
            ->afterCommit();

        $this->activity->handle(
            type: ActivityType::InvoiceReminderSent,
            description: "Reminder queued for {$invoice->invoice_number}.",
            actor: $actor,
            subject: $invoice,
            metadata: [
                'client_email' => $invoice->client->email,
                'reminders_sent' => (int) $invoice->reminders_sent,
            ],
        );
    }
}
