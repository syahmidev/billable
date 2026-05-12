<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Activity\RecordActivity;
use App\Actions\Billing\ActivateFreePlan;
use App\Actions\Billing\StartSubscription;
use App\Enums\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Queries\Tenant\BillingOwnerQuery;
use App\ViewModels\Tenant\BillingOverviewViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BillingController extends Controller
{
    public function index(Request $request, BillingOverviewViewModel $billing): Response
    {
        $user = $request->user();
        Gate::authorize('view-workspace-billing');

        return Inertia::render('Tenant/Billing/Index', $billing->toArray($user));
    }

    public function subscribe(
        Request $request,
        Plan $plan,
        ActivateFreePlan $freePlan,
        StartSubscription $startSubscription,
        RecordActivity $activity,
        BillingOwnerQuery $billingOwnerQuery,
    ): RedirectResponse|SymfonyResponse {
        $user = $request->user();
        $billingOwner = $billingOwnerQuery->handle((string) tenant('id')) ?? $user;

        Gate::authorize('manage-workspace-billing', $billingOwner);

        if ($user->subscribed('default')) {
            return redirect()
                ->route('tenant.billing.index')
                ->with('error', 'Use the billing portal to manage your current subscription.');
        }

        if ($plan->isFree()) {
            $freePlan->handle($user);
            $activity->handle(
                type: ActivityType::BillingPlanChanged,
                description: 'Free plan was activated.',
                actor: $user,
                metadata: ['plan' => $plan->slug],
            );

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

        $activity->handle(
            type: ActivityType::BillingPlanChanged,
            description: "{$plan->name} checkout was started.",
            actor: $user,
            metadata: ['plan' => $plan->slug],
        );

        return Inertia::location($checkout->url);
    }

    public function portal(Request $request, BillingOwnerQuery $billingOwnerQuery): SymfonyResponse
    {
        $user = $request->user();
        $billingOwner = $billingOwnerQuery->handle((string) tenant('id')) ?? $user;

        Gate::authorize('manage-workspace-billing', $billingOwner);
        abort_unless($user->hasStripeId(), 404);

        $url = $user->billingPortalUrl(route('tenant.billing.index'));

        return Inertia::location($url);
    }
}
