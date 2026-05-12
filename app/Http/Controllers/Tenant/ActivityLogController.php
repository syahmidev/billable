<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Queries\Tenant\ActivityLogListingQuery;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(ActivityLogListingQuery $activities): Response
    {
        Gate::authorize('view-workspace-activity');

        return Inertia::render('Tenant/Activity/Index', [
            'activities' => $activities->handle(),
        ]);
    }
}
