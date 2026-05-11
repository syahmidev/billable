<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((bool) tenant('is_suspended')) {
            abort(403, 'This workspace is suspended.');
        }

        return $next($request);
    }
}
