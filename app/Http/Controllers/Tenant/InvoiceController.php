<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Invoice\CreateInvoice;
use App\Actions\Invoice\SendInvoice;
use App\Actions\Invoice\UpdateInvoice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SaveInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Support\AppUrl;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = Invoice::with('client')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->client_id, fn ($q, $id) => $q->where('client_id', $id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Tenant/Invoices/Index', [
            'invoices' => $invoices,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('status', 'client_id'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenant/Invoices/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'defaultIssueDate' => now()->toDateString(),
            'defaultDueDate' => now()->addDays(30)->toDateString(),
        ]);
    }

    public function store(SaveInvoiceRequest $request, CreateInvoice $action): RedirectResponse
    {
        $invoice = $action->handle($request->validated());

        return redirect()->route('tenant.invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load('client', 'items');

        $domain = tenant()->domains->first()?->domain;
        $paymentUrl = ($domain && $invoice->payment_token)
            ? AppUrl::tenant($domain, "/pay/{$invoice->payment_token}")
            : null;

        return Inertia::render('Tenant/Invoices/Show', [
            'invoice' => [
                ...$invoice->toArray(),
                'discount_amount' => round($invoice->discountAmount(), 2),
                'tax_amount' => round($invoice->taxAmount(), 2),
            ],
            'workspaceName' => tenant('name'),
            'paymentUrl' => $paymentUrl,
        ]);
    }

    public function edit(Invoice $invoice): Response
    {
        $invoice->load('client', 'items');

        return Inertia::render('Tenant/Invoices/Edit', [
            'invoice' => $invoice,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(SaveInvoiceRequest $request, Invoice $invoice, UpdateInvoice $action): RedirectResponse
    {
        $action->handle($invoice, $request->validated());

        return redirect()->route('tenant.invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('tenant.invoices.index')->with('success', 'Invoice deleted.');
    }

    public function send(Invoice $invoice, SendInvoice $action): RedirectResponse
    {
        $action->handle($invoice, tenant('name'));

        $message = $invoice->client->email
            ? 'Invoice sent to '.$invoice->client->email
            : 'Invoice marked as sent (no client email on file).';

        return redirect()->route('tenant.invoices.show', $invoice)->with('success', $message);
    }

    public function pdf(Invoice $invoice): HttpResponse
    {
        $invoice->load('client', 'items');

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'workspaceName' => tenant('name'),
        ]);

        return $pdf->download("{$invoice->invoice_number}.pdf");
    }
}
