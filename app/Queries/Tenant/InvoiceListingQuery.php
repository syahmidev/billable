<?php

declare(strict_types=1);

namespace App\Queries\Tenant;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceListingQuery
{
    public function handle(array $filters = []): LengthAwarePaginator
    {
        return Invoice::with('client')
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['client_id'] ?? null, fn (Builder $query, string $clientId) => $query->where('client_id', $clientId))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
