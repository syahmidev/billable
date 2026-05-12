<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-workspace-activity', fn (User $user): bool => $this->hasTenantPermission(
            user: $user,
            permission: Permission::ActivityView,
        ));

        Gate::define('view-workspace-billing', fn (User $user): bool => $this->hasTenantPermission(
            user: $user,
            permission: Permission::BillingView,
        ));

        Gate::define('manage-workspace-billing', fn (User $user, User $billingOwner): bool => $user->is($billingOwner)
            && $this->hasTenantPermission(
                user: $user,
                permission: Permission::BillingManage,
            ));

        Gate::define('view-team-members', fn (User $user): bool => $this->hasTenantPermission(
            user: $user,
            permission: Permission::TeamView,
        ));

        Gate::define('manage-team-members', fn (User $user): bool => $this->hasTenantPermission(
            user: $user,
            permission: Permission::TeamManage,
        ));
    }

    private function hasTenantPermission(User $user, Permission $permission): bool
    {
        $tenantId = tenant('id');

        return is_string($tenantId)
            && $user->belongsToTenant($tenantId)
            && $user->hasTenantPermission($permission);
    }
}
