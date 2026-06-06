<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Support\AppUrl;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function __invoke(): Response
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('price')
            ->get(['name', 'slug', 'price', 'features']);

        $workspaceUrl = null;
        if ($user = Auth::user()) {
            $domain = Tenant::find($user->tenant_id)?->domains()->value('domain');
            if ($domain) {
                $workspaceUrl = AppUrl::tenant($domain);
            }
        }

        return Inertia::render('Landing', [
            'plans' => $plans,
            'workspace_url' => $workspaceUrl,
        ]);
    }
}
