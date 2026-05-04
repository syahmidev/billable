<?php

namespace App\Http\Middleware;

use App\Enums\PlanSlug;
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

        return redirect()->away(AppUrl::central('/plans'));
    }
}
