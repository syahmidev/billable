<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;

class UpdateInvoice
{
    public function handle(Invoice $invoice, array $data): Invoice
    {
        $subtotal = collect($data['items'])->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
        $discountAmount = $subtotal * (($data['discount_percent'] ?? 0) / 100);
        $taxAmount = ($subtotal - $discountAmount) * (($data['tax_percent'] ?? 0) / 100);

        $invoice->update([
            'client_id' => $data['client_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'total' => $subtotal - $discountAmount + $taxAmount,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();

        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return $invoice->fresh('items');
    }
}
