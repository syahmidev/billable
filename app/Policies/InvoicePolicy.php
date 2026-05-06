<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function create(User $user): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }

    public function remind(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'))
            && in_array($invoice->status, Invoice::remindableStatuses(), true);
    }

    public function pdf(User $user, Invoice $invoice): bool
    {
        return $user->belongsToTenant(tenant('id'));
    }
}
