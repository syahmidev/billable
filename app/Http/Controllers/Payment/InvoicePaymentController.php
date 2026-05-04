<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Actions\Invoice\CreatePaymentIntent;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvoicePaymentController extends Controller
{
    public function show(string $token): Response
    {
        $invoice = Invoice::with('client', 'items')
            ->where('payment_token', $token)
            ->firstOrFail();

        return Inertia::render('Payment/InvoicePay', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date->format('M d, Y'),
                'due_date' => $invoice->due_date->format('M d, Y'),
                'total' => (float) $invoice->total,
                'payment_token' => $invoice->payment_token,
                'client' => [
                    'name' => $invoice->client->name,
                    'email' => $invoice->client->email,
                    'company' => $invoice->client->company,
                ],
                'items' => $invoice->items->map(fn ($i) => [
                    'description' => $i->description,
                    'quantity' => (float) $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'line_total' => (float) $i->line_total,
                ]),
            ],
            'workspaceName' => tenant('name'),
            'stripeKey' => config('cashier.key'),
            'alreadyPaid' => $invoice->status === 'paid',
        ]);
    }

    public function createIntent(string $token, CreatePaymentIntent $action): JsonResponse
    {
        $invoice = Invoice::where('payment_token', $token)
            ->whereIn('status', ['sent', 'overdue'])
            ->firstOrFail();

        $clientSecret = $action->handle($invoice);

        return response()->json(['clientSecret' => $clientSecret]);
    }
}
