<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'actor_id', 'actor_name', 'actor_email', 'subject_type', 'subject_id', 'description', 'metadata', 'occurred_at'])]
class ActivityLog extends Model
{
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'datetime',
            'type' => ActivityType::class,
        ];
    }
}
