<?php

namespace App\Http\Controllers\Billing;

use App\Actions\Billing\ActivateFreePlan;
use App\Actions\Billing\StartSubscription;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PlanController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->plan === 'free' || $user->subscribed('default')) {
            if ($url = $user->tenantUrl()) {
                return redirect()->away($url);
            }
        }

        return Inertia::render('Billing/Plans', [
            'plans' => Plan::where('is_active', true)->orderBy('price')->get()->map(fn ($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'price' => $plan->price,
                'formatted_price' => $plan->formattedPrice(),
                'features' => $plan->features,
                'is_free' => $plan->isFree(),
            ]),
        ]);
    }

    public function subscribe(Request $request, Plan $plan, ActivateFreePlan $freePlan, StartSubscription $startSubscription): RedirectResponse|SymfonyResponse
    {
        $user = $request->user();

        if ($plan->isFree()) {
            $freePlan->handle($user);
            return Inertia::location($user->tenantUrl());
        }

        $checkout = $startSubscription->handle($user, $plan);

        return Inertia::location($checkout->url);
    }

    public function success(Request $request): SymfonyResponse
    {
        return Inertia::location($request->user()->tenantUrl() ?? route('plans'));
    }

    public function portal(Request $request): SymfonyResponse
    {
        $url = $request->user()->billingPortalUrl(route('plans'));

        return Inertia::location($url);
    }
}
