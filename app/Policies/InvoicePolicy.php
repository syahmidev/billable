<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesView);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesView);
    }

    public function create(User $user): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesCreate);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesUpdate);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesDelete);
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesSend);
    }

    public function remind(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesRemind)
            && in_array($invoice->status, Invoice::remindableStatuses(), true);
    }

    public function pdf(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && $user->hasTenantPermission(Permission::InvoicesView);
    }
}
