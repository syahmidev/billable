<?php

declare(strict_types=1);

namespace App\Queries\Tenant;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;

class TeamMemberListingQuery
{
    public function handle(string $tenantId, User $currentUser): Collection
    {
        return User::query()
            ->where('tenant_id', $tenantId)
            ->get()
            ->sortBy(fn (User $member): string => ($member->role === UserRole::Owner->value ? '0' : '1').strtolower($member->name))
            ->values()
            ->map(fn (User $member): array => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->role,
                'is_current_user' => $member->is($currentUser),
                'created_at' => $member->created_at?->format('M d, Y'),
            ]);
    }
}
