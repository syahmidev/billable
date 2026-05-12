<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashierWebhookSecretIsConfigured
{
    public function handle(Request $request, Closure $next): Response
    {
        $webhookSecret = config('cashier.webhook.secret');

        if (! is_string($webhookSecret) || $webhookSecret === '') {
            return response('Cashier webhook secret is not configured.', 500);
        }

        return $next($request);
    }
}
