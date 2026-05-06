<?php

declare(strict_types=1);

namespace App\Actions\Team;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RemoveTeamMember
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(User $member, User $actor): void
    {
        if ($member->is($actor)) {
            throw ValidationException::withMessages([
                'member' => 'You cannot remove yourself from the workspace.',
            ]);
        }

        $memberName = $member->name;
        $memberId = $member->id;
        $memberEmail = $member->email;

        $member->delete();

        $this->activity->handle(
            type: ActivityType::TeamMemberRemoved,
            description: "{$memberName} was removed from the team.",
            actor: $actor,
            metadata: [
                'member_id' => $memberId,
                'member_email' => $memberEmail,
            ],
        );
    }
}
