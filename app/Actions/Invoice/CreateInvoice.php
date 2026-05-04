<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Support\InvoiceTotals;
use Illuminate\Support\Str;

class CreateInvoice
{
    public function handle(array $data): Invoice
    {
        $totals = InvoiceTotals::fromItems(
            $data['items'],
            $data['discount_percent'] ?? 0,
            $data['tax_percent'] ?? 0,
        );

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'client_id' => $data['client_id'],
            'status' => InvoiceStatus::Draft->value,
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $totals->subtotal,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'total' => $totals->total,
            'notes' => $data['notes'] ?? null,
            'payment_token' => (string) Str::uuid(),
        ]);

        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => InvoiceTotals::lineTotal($item),
            ]);
        }

        return $invoice;
    }
}
