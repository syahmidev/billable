<?php

namespace App\Concerns;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Support\AppUrl;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTenantAccess
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner->value;
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

        return AppUrl::tenant($domain, $path);
    }
}
