<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Permission;
use App\Support\AppUrl;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'tenant_id' => $request->user()->tenant_id,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'permissions' => $this->permissions($request),
            'seo' => [
                'site_name' => config('seo.site_name'),
                'title' => config('seo.title'),
                'description' => config('seo.description'),
                'image' => config('seo.image'),
                'twitter_site' => config('seo.twitter_site'),
                'base_url' => rtrim(AppUrl::central('/'), '/'),
                'current_url' => $request->url(),
            ],
        ]);
    }

    private function permissions(Request $request): array
    {
        $user = $request->user();
        $tenant = tenant();

        if (! $user || ! $tenant || ! $user->belongsToTenant((string) tenant('id'))) {
            return [];
        }

        return [
            'activity' => [
                'view' => $user->hasTenantPermission(Permission::ActivityView),
            ],
            'billing' => [
                'view' => $user->hasTenantPermission(Permission::BillingView),
                'manage' => $user->hasTenantPermission(Permission::BillingManage),
            ],
            'clients' => [
                'view' => $user->hasTenantPermission(Permission::ClientsView),
                'create' => $user->hasTenantPermission(Permission::ClientsCreate),
                'update' => $user->hasTenantPermission(Permission::ClientsUpdate),
                'delete' => $user->hasTenantPermission(Permission::ClientsDelete),
            ],
            'invoices' => [
                'view' => $user->hasTenantPermission(Permission::InvoicesView),
                'create' => $user->hasTenantPermission(Permission::InvoicesCreate),
                'update' => $user->hasTenantPermission(Permission::InvoicesUpdate),
                'delete' => $user->hasTenantPermission(Permission::InvoicesDelete),
                'send' => $user->hasTenantPermission(Permission::InvoicesSend),
                'remind' => $user->hasTenantPermission(Permission::InvoicesRemind),
            ],
            'team' => [
                'view' => $user->hasTenantPermission(Permission::TeamView),
                'manage' => $user->hasTenantPermission(Permission::TeamManage),
            ],
        ];
    }
}
