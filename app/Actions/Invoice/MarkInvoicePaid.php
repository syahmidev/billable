<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Mail\PaymentReceiptMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class MarkInvoicePaid
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, string $workspaceName, ?string $paymentIntentId = null): void
    {
        if ($invoice->status === InvoiceStatus::Paid->value) {
            return;
        }

        $invoice->forceFill([
            'status' => InvoiceStatus::Paid->value,
            'stripe_payment_intent_id' => $invoice->stripe_payment_intent_id ?: $paymentIntentId,
        ])->save();

        if ($invoice->client->email) {
            Mail::to($invoice->client->email)
                ->send(new PaymentReceiptMail($invoice->load('client', 'items'), $workspaceName));
        }

        $this->activity->handle(
            type: ActivityType::InvoicePaid,
            description: "{$invoice->invoice_number} was marked paid.",
            subject: $invoice,
            metadata: [
                'payment_intent_id' => $paymentIntentId,
                'total' => (float) $invoice->total,
            ],
        );
    }
}
