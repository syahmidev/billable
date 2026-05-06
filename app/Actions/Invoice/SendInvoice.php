<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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
            Mail::to($invoice->client->email)
                ->send(new InvoiceMail($invoice->load('client', 'items'), $workspaceName));
        }

        $this->activity->handle(
            type: ActivityType::InvoiceSent,
            description: "{$invoice->invoice_number} was sent.",
            actor: $actor,
            subject: $invoice,
            metadata: ['client_email' => $invoice->client->email],
        );
    }
}
