<?php

namespace App\Actions\Billing;

use App\Models\User;

class ActivateFreePlan
{
    public function handle(User $user): void
    {
        $user->update(['plan' => 'free']);
    }
}
