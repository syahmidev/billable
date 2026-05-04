<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->plan === 'free' || $user->subscribed('default')) {
            return $next($request);
        }

        $scheme = parse_url(config('app.url'), PHP_URL_SCHEME);
        $host = parse_url(config('app.url'), PHP_URL_HOST);

        return redirect()->away("{$scheme}://{$host}/plans");
    }
}
