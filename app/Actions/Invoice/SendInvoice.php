<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Jobs\Invoice\SendInvoiceEmail;
use App\Models\Invoice;
use App\Models\User;

class SendInvoice
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, string $workspaceName, ?User $actor = null): void
    {
        $invoice->update([
            'status' => InvoiceStatus::Sent->value,
            'sent_at' => now(),
        ]);

        if ($invoice->client->email) {
            SendInvoiceEmail::dispatch($invoice->id, $invoice->client->email, $workspaceName)
                ->afterCommit();
        }

        $this->activity->handle(
            type: ActivityType::InvoiceSent,
            description: "{$invoice->invoice_number} was queued for delivery.",
            actor: $actor,
            subject: $invoice,
            metadata: ['client_email' => $invoice->client->email],
        );
    }
}
