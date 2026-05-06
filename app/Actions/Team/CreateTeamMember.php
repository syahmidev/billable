<?php

declare(strict_types=1);

namespace App\Actions\Team;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTeamMember
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(array $data, User $actor, string $tenantId): User
    {
        $member = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $tenantId,
            'role' => $data['role'] ?? UserRole::Member->value,
            'plan' => $actor->plan,
        ]);

        $this->activity->handle(
            type: ActivityType::TeamMemberAdded,
            description: "{$member->name} was added to the team.",
            actor: $actor,
            metadata: [
                'member_id' => $member->id,
                'member_email' => $member->email,
                'role' => $member->role,
            ],
        );

        return $member;
    }
}
