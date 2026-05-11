<?php

declare(strict_types=1);

namespace App\Queries\Tenant;

use App\Enums\UserRole;
use App\Models\User;

class BillingOwnerQuery
{
    public function handle(string $tenantId): ?User
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->where('role', UserRole::Owner->value)
            ->oldest('id')
            ->first();
    }
}
