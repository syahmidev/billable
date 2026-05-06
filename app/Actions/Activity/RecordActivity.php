<?php

declare(strict_types=1);

namespace App\Actions\Activity;

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RecordActivity
{
    public function handle(
        ActivityType $type,
        string $description,
        ?User $actor = null,
        ?Model $subject = null,
        array $metadata = [],
    ): ActivityLog {
        return ActivityLog::create([
            'type' => $type,
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'actor_email' => $actor?->email,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey() !== null ? (string) $subject->getKey() : null,
            'description' => $description,
            'metadata' => $metadata ?: null,
            'occurred_at' => now(),
        ]);
    }
}
