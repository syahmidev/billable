<?php

namespace App\Concerns;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTenantAccess
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function belongsToTenant(string $tenantId): bool
    {
        return $this->tenant_id === $tenantId;
    }

    public function tenantUrl(string $path = '/dashboard'): ?string
    {
        $domain = $this->tenant?->domains()->first()?->domain;

        if (! $domain) {
            return null;
        }

        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME);

        return "{$scheme}://{$domain}{$path}";
    }
}
