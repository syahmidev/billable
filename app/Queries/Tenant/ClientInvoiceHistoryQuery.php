<?php

declare(strict_types=1);

namespace App\Queries\Tenant;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientInvoiceHistoryQuery
{
    public function handle(Client $client): Collection
    {
        return $client->invoices()
            ->with('items')
            ->latest()
            ->get()
            ->map(fn ($invoice): array => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date->format('M d, Y'),
                'due_date' => $invoice->due_date->format('M d, Y'),
                'total' => (float) $invoice->total,
            ]);
    }
}
