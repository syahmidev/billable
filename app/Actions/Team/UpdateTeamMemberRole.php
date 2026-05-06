<?php

declare(strict_types=1);

namespace App\Actions\Team;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdateTeamMemberRole
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(User $member, User $actor, UserRole $role): void
    {
        if ($member->is($actor) && $role !== UserRole::Owner) {
            throw ValidationException::withMessages([
                'role' => 'You cannot remove your own owner access.',
            ]);
        }

        $member->update(['role' => $role->value]);

        $this->activity->handle(
            type: ActivityType::TeamMemberUpdated,
            description: "{$member->name} was changed to {$role->label()}.",
            actor: $actor,
            metadata: [
                'member_id' => $member->id,
                'member_email' => $member->email,
                'role' => $role->value,
            ],
        );
    }
}
