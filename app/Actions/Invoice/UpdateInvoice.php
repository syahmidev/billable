<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Support\InvoiceTotals;

class UpdateInvoice
{
    public function handle(Invoice $invoice, array $data): Invoice
    {
        $totals = InvoiceTotals::fromItems(
            $data['items'],
            $data['discount_percent'] ?? 0,
            $data['tax_percent'] ?? 0,
        );

        $invoice->update([
            'client_id' => $data['client_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $totals->subtotal,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'total' => $totals->total,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();

        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => InvoiceTotals::lineTotal($item),
            ]);
        }

        return $invoice->fresh('items');
    }
}
