<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class SendInvoice
{
    public function handle(Invoice $invoice, string $workspaceName): void
    {
        $invoice->update([
            'status' => InvoiceStatus::Sent->value,
            'sent_at' => now(),
        ]);

        if ($invoice->client->email) {
            Mail::to($invoice->client->email)
                ->send(new InvoiceMail($invoice->load('client', 'items'), $workspaceName));
        }
    }
}
