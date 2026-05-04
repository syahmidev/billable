<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CreatePaymentIntent
{
    public function handle(Invoice $invoice): string
    {
        Stripe::setApiKey(config('cashier.secret'));

        $intent = PaymentIntent::create([
            'amount' => (int) round((float) $invoice->total * 100),
            'currency' => strtolower((string) config('cashier.currency', 'usd')),
            'metadata' => [
                'invoice_id' => $invoice->id,
                'tenant_id' => tenant('id'),
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        $invoice->update(['stripe_payment_intent_id' => $intent->id]);

        return $intent->client_secret;
    }
}
