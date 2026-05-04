<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Mail\PaymentReceiptMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class MarkInvoicePaid
{
    public function handle(Invoice $invoice, string $workspaceName): void
    {
        $invoice->update(['status' => InvoiceStatus::Paid->value]);

        if ($invoice->client->email) {
            Mail::to($invoice->client->email)
                ->send(new PaymentReceiptMail($invoice->load('client', 'items'), $workspaceName));
        }
    }
}
