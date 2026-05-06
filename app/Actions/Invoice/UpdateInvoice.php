<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Invoice;
use App\Models\User;
use App\Support\InvoiceTotals;

class UpdateInvoice
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, array $data, ?User $actor = null): Invoice
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

        $invoice = $invoice->fresh('items');

        $this->activity->handle(
            type: ActivityType::InvoiceUpdated,
            description: "{$invoice->invoice_number} was updated.",
            actor: $actor,
            subject: $invoice,
            metadata: ['total' => (float) $invoice->total],
        );

        return $invoice;
    }
}
