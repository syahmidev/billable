<?php

namespace App\Actions\Billing;

use App\Enums\PlanSlug;
use App\Models\User;

class ActivateFreePlan
{
    public function handle(User $user): void
    {
        $user->update(['plan' => PlanSlug::Free->value]);
    }
}
