<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stripe;

use App\Actions\Invoice\MarkInvoicePaid;
use App\Enums\InvoiceStatus;
use App\Enums\StripeEventStatus;
use App\Enums\StripeEventType;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StripeEvent;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Facades\Tenancy;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;

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

        $eventLog = StripeEvent::createOrFirst(
            ['stripe_event_id' => $event->id],
            [
                'type' => $event->type,
                'payload' => json_decode($payload, true) ?: [],
                'status' => StripeEventStatus::Received,
            ],
        );

        if ($eventLog->status?->isHandled() === true) {
            return response('Event already handled.', 200);
        }

        $eventLog->recordAttempt();

        if ($event->type !== StripeEventType::PaymentIntentSucceeded->value) {
            $eventLog->markIgnored('Unsupported event type.');

            return response('Event ignored.', 200);
        }

        $paymentIntent = $event->data->object;
        $paymentIntentId = (string) $paymentIntent->id;
        $tenantId = $paymentIntent->metadata->tenant_id ?? null;
        $invoiceId = $paymentIntent->metadata->invoice_id ?? null;

        $eventLog->forceFill([
            'tenant_id' => $tenantId,
            'invoice_id' => $invoiceId,
            'payment_intent_id' => $paymentIntentId,
        ])->save();

        if (! $tenantId || ! $invoiceId) {
            $eventLog->markIgnored('Missing metadata.');

            return response('Missing metadata.', 200);
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $eventLog->markIgnored('Tenant not found.');

            return response('Tenant not found.', 200);
        }

        Tenancy::initialize($tenant);

        try {
            $invoice = Invoice::find($invoiceId);

            if (! $invoice) {
                $eventLog->markIgnored('Invoice not found.');

                return response('Invoice not found.', 200);
            }

            if (
                $invoice->stripe_payment_intent_id
                && $invoice->stripe_payment_intent_id !== $paymentIntentId
            ) {
                $eventLog->markFailed('Payment intent mismatch.');

                return response('Payment intent mismatch.', 200);
            }

            if ($invoice->status === InvoiceStatus::Paid->value) {
                $eventLog->markProcessed();

                return response('Invoice already paid.', 200);
            }

            $action->handle(
                invoice: $invoice->load('client', 'items'),
                workspaceName: $tenant->name ?? 'billable',
                paymentIntentId: $paymentIntentId,
            );

            $eventLog->markProcessed();
        } catch (Throwable $exception) {
            $eventLog->markFailed($exception->getMessage());

            throw $exception;
        } finally {
            Tenancy::end();
        }

        return response('Webhook handled.', 200);
    }
}
