<?php

declare(strict_types=1);

namespace App\Actions\Tenant;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use App\Support\AppUrl;
use Stancl\Tenancy\Database\Models\Domain;

class CreateWorkspace
{
    private const RESERVED_SUBDOMAINS = [
        'admin', 'api', 'www', 'app', 'mail', 'smtp',
        'ftp', 'billing', 'help', 'support', 'status', 'dashboard',
    ];

    public function isSubdomainTaken(string $subdomain): bool
    {
        if (in_array($subdomain, self::RESERVED_SUBDOMAINS, true)) {
            return true;
        }

        $domain = AppUrl::tenantDomain($subdomain);

        return Domain::where('domain', $domain)->exists();
    }

    public function handle(User $user, string $workspaceName, string $subdomain): void
    {
        $domain = AppUrl::tenantDomain($subdomain);

        $tenant = Tenant::create(['name' => $workspaceName]);
        $tenant->domains()->create(['domain' => $domain]);

        $user->update(['tenant_id' => $tenant->id, 'role' => UserRole::Owner->value]);
    }
}
