<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stripe;

use App\Actions\Invoice\MarkInvoicePaid;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Facades\Tenancy;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class InvoiceWebhookController extends Controller
{
    public function handle(Request $request, MarkInvoicePaid $action): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('cashier.webhook.secret')
            );
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        if ($event->type !== 'payment_intent.succeeded') {
            return response('Event ignored.', 200);
        }

        $paymentIntent = $event->data->object;
        $paymentIntentId = (string) $paymentIntent->id;
        $tenantId = $paymentIntent->metadata->tenant_id ?? null;
        $invoiceId = $paymentIntent->metadata->invoice_id ?? null;

        if (! $tenantId || ! $invoiceId) {
            return response('Missing metadata.', 200);
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            return response('Tenant not found.', 200);
        }

        Tenancy::initialize($tenant);

        try {
            $invoice = Invoice::find($invoiceId);

            if (! $invoice) {
                return response('Invoice not found.', 200);
            }

            if (
                $invoice->stripe_payment_intent_id
                && $invoice->stripe_payment_intent_id !== $paymentIntentId
            ) {
                return response('Payment intent mismatch.', 200);
            }

            if ($invoice->status === InvoiceStatus::Paid->value) {
                return response('Invoice already paid.', 200);
            }

            $action->handle(
                invoice: $invoice->load('client', 'items'),
                workspaceName: $tenant->name ?? 'billable',
                paymentIntentId: $paymentIntentId,
            );
        } finally {
            Tenancy::end();
        }

        return response('Webhook handled.', 200);
    }
}
