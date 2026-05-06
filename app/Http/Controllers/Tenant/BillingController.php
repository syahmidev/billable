<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Activity\RecordActivity;
use App\Actions\Billing\ActivateFreePlan;
use App\Actions\Billing\StartSubscription;
use App\Enums\ActivityType;
use App\Enums\PlanSlug;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
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
        $billingOwner = $this->billingOwner() ?? $user;
        $canManageBilling = $user->is($billingOwner);
        $subscription = $billingOwner->subscription('default');
        $currentPlan = $this->currentPlan($billingOwner, $subscription);
        $status = $subscription?->stripe_status ?? ($billingOwner->plan ?: 'none');
        $subscriptionStatus = SubscriptionStatus::tryFrom((string) $status);

        return Inertia::render('Tenant/Billing/Index', [
            'workspace' => [
                'name' => tenant('name'),
                'id' => tenant('id'),
            ],
            'billing' => [
                'current_plan' => $currentPlan ? $this->planPayload($currentPlan) : null,
                'status' => $status,
                'status_label' => $subscriptionStatus?->label(),
                'status_message' => $subscriptionStatus?->message(),
                'status_tone' => $subscriptionStatus?->tone(),
                'needs_attention' => $subscriptionStatus?->needsAttention() ?? false,
                'is_free' => $billingOwner->plan === PlanSlug::Free->value && ! $billingOwner->subscribed('default'),
                'is_subscribed' => $billingOwner->subscribed('default'),
                'ends_at' => $subscription?->ends_at?->toDateString(),
                'portal_url' => $canManageBilling && $subscription && $billingOwner->hasStripeId()
                    ? route('tenant.billing.portal')
                    : null,
                'can_manage' => $canManageBilling,
                'is_managed_by_owner' => ! $canManageBilling,
                'owner' => [
                    'name' => $billingOwner->name,
                    'email' => $billingOwner->email,
                ],
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
        RecordActivity $activity,
    ): RedirectResponse|SymfonyResponse {
        $user = $request->user();
        $billingOwner = $this->billingOwner() ?? $user;

        abort_unless($user->is($billingOwner), 403);

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

    public function portal(Request $request): SymfonyResponse
    {
        $billingOwner = $this->billingOwner() ?? $request->user();

        abort_unless($request->user()->is($billingOwner), 403);
        abort_unless($request->user()->hasStripeId(), 404);

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

    private function billingOwner(): ?User
    {
        return User::query()
            ->where('tenant_id', tenant('id'))
            ->where('role', UserRole::Owner->value)
            ->oldest('id')
            ->first();
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
