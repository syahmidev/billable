<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Jobs\Invoice\SendPaymentReceiptEmail;
use App\Models\Invoice;

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
            'paid_at' => now(),
            'stripe_payment_intent_id' => $invoice->stripe_payment_intent_id ?: $paymentIntentId,
        ])->save();

        if ($invoice->client->email) {
            SendPaymentReceiptEmail::dispatch($invoice->id, $invoice->client->email, $workspaceName)
                ->afterCommit();
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
