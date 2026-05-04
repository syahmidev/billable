<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Stancl\Tenancy\Database\Models\Domain;

class CreateWorkspace
{
    private const RESERVED_SUBDOMAINS = [
        'admin', 'api', 'www', 'app', 'mail', 'smtp',
        'ftp', 'billing', 'help', 'support', 'status', 'dashboard',
    ];

    public function isSubdomainTaken(string $subdomain): bool
    {
        if (in_array($subdomain, self::RESERVED_SUBDOMAINS)) {
            return true;
        }

        $domain = $this->buildDomain($subdomain);

        return Domain::where('domain', $domain)->exists();
    }

    public function handle(User $user, string $workspaceName, string $subdomain): string
    {
        $domain = $this->buildDomain($subdomain);

        $tenant = Tenant::create(['name' => $workspaceName]);
        $tenant->domains()->create(['domain' => $domain]);

        $user->update(['tenant_id' => $tenant->id, 'role' => 'owner']);

        return $this->tenantUrl($domain);
    }

    private function buildDomain(string $subdomain): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST);

        return "{$subdomain}.{$host}";
    }

    private function tenantUrl(string $domain): string
    {
        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME);

        return "{$scheme}://{$domain}/dashboard";
    }
}
