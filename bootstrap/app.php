<?php

declare(strict_types=1);

use App\Exceptions\PlanLimitExceededException;
use App\Http\Middleware\EnsureSubscribed;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\EnsureTenantMember;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'tenant.active' => EnsureTenantIsActive::class,
            'tenant.member' => EnsureTenantMember::class,
            'subscribed' => EnsureSubscribed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PlanLimitExceededException $e) {
            return back()->with('error', $e->getMessage());
        });
    })->create();
