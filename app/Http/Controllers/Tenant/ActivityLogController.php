<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        $activities = ActivityLog::query()
            ->latest('occurred_at')
            ->paginate(20)
            ->through(fn (ActivityLog $activity): array => [
                'id' => $activity->id,
                'type' => $activity->type->value,
                'label' => $activity->type->label(),
                'description' => $activity->description,
                'actor_name' => $activity->actor_name,
                'actor_email' => $activity->actor_email,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'metadata' => $activity->metadata,
                'occurred_at' => $activity->occurred_at?->diffForHumans(),
                'occurred_at_full' => $activity->occurred_at?->format('M d, Y h:i A'),
            ]);

        return Inertia::render('Tenant/Activity/Index', [
            'activities' => $activities,
        ]);
    }
}
