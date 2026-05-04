<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Billing\ActivateFreePlan;
use App\Actions\Billing\StartSubscription;
use App\Enums\PlanSlug;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BillingController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $subscription = $user->subscription('default');
        $currentPlan = $this->currentPlan($user, $subscription);

        return Inertia::render('Tenant/Billing/Index', [
            'workspace' => [
                'name' => tenant('name'),
                'id' => tenant('id'),
            ],
            'billing' => [
                'current_plan' => $currentPlan ? $this->planPayload($currentPlan) : null,
                'status' => $subscription?->stripe_status ?? ($user->plan ?: 'none'),
                'is_free' => $user->plan === PlanSlug::Free->value && ! $user->subscribed('default'),
                'is_subscribed' => $user->subscribed('default'),
                'ends_at' => $subscription?->ends_at?->toDateString(),
                'portal_url' => $user->subscribed('default') ? route('tenant.billing.portal') : null,
            ],
            'plans' => Plan::where('is_active', true)
                ->orderBy('price')
                ->get()
                ->map(fn (Plan $plan): array => $this->planPayload($plan)),
        ]);
    }

    public function subscribe(
        Request $request,
        Plan $plan,
        ActivateFreePlan $freePlan,
        StartSubscription $startSubscription,
    ): RedirectResponse|SymfonyResponse {
        $user = $request->user();

        if ($user->subscribed('default')) {
            return redirect()
                ->route('tenant.billing.index')
                ->with('error', 'Use the billing portal to manage your current subscription.');
        }

        if ($plan->isFree()) {
            $freePlan->handle($user);

            return redirect()
                ->route('tenant.billing.index')
                ->with('success', 'Free plan activated.');
        }

        $checkout = $startSubscription->handle(
            user: $user,
            plan: $plan,
            successUrl: route('tenant.billing.index').'?checkout=success',
            cancelUrl: route('tenant.billing.index'),
        );

        return Inertia::location($checkout->url);
    }

    public function portal(Request $request): SymfonyResponse
    {
        $url = $request->user()->billingPortalUrl(route('tenant.billing.index'));

        return Inertia::location($url);
    }

    private function currentPlan(mixed $user, mixed $subscription): ?Plan
    {
        if ($subscription?->stripe_price) {
            $plan = Plan::where('stripe_price_id', $subscription->stripe_price)->first();

            if ($plan) {
                return $plan;
            }
        }

        if ($user->plan) {
            return Plan::where('slug', $user->plan)->first();
        }

        return null;
    }

    private function planPayload(Plan $plan): array
    {
        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'slug' => $plan->slug,
            'price' => $plan->price,
            'formatted_price' => $plan->formattedPrice(),
            'features' => $plan->features,
            'is_free' => $plan->isFree(),
        ];
    }
}
