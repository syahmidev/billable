<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanSlug;
use App\Enums\UserRole;
use App\Models\User;
use App\Support\AppUrl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->plan === PlanSlug::Free->value || $user->subscribed('default')) {
            return $next($request);
        }

        $owner = User::query()
            ->where('tenant_id', tenant('id'))
            ->where('role', UserRole::Owner->value)
            ->first();

        if ($owner && ($owner->plan === PlanSlug::Free->value || $owner->subscribed('default'))) {
            return $next($request);
        }

        return redirect()->away(AppUrl::central('/plans'));
    }
}
