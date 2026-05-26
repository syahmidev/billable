<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Throwable;

class DeleteInvoice
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, ?User $actor = null): void
    {
        $invoiceNumber = $invoice->invoice_number;
        $invoiceId = $invoice->id;

        $this->cancelStripePaymentIntent($invoice);

        $invoice->delete();

        $this->activity->handle(
            type: ActivityType::InvoiceDeleted,
            description: "{$invoiceNumber} was deleted.",
            actor: $actor,
            metadata: ['invoice_id' => $invoiceId],
        );
    }

    private function cancelStripePaymentIntent(Invoice $invoice): void
    {
        if (! $invoice->stripe_payment_intent_id) {
            return;
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));

            $intent = PaymentIntent::retrieve($invoice->stripe_payment_intent_id);

            // Only cancellable when not yet in a terminal state.
            if (! in_array($intent->status, ['succeeded', 'canceled'], strict: true)) {
                $intent->cancel();
            }
        } catch (Throwable $e) {
            // Do not block deletion — log for observability.
            Log::warning('Failed to cancel Stripe PaymentIntent on invoice deletion.', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'payment_intent_id' => $invoice->stripe_payment_intent_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
