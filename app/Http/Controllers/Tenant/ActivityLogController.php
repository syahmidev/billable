<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Queries\Tenant\ActivityLogListingQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request, ActivityLogListingQuery $activities): Response
    {
        abort_unless($request->user()?->hasTenantPermission(Permission::ActivityView), 403);

        return Inertia::render('Tenant/Activity/Index', [
            'activities' => $activities->handle(),
        ]);
    }
}
