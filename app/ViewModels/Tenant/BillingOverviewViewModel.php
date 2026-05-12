<?php

declare(strict_types=1);

namespace App\ViewModels\Tenant;

use App\Enums\PlanSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\User;
use App\Queries\Tenant\BillingOwnerQuery;
use Illuminate\Support\Facades\Gate;

class BillingOverviewViewModel
{
    public function __construct(private readonly BillingOwnerQuery $billingOwnerQuery) {}

    public function toArray(User $user): array
    {
        $billingOwner = $this->billingOwnerQuery->handle((string) tenant('id')) ?? $user;
        $canManageBilling = Gate::forUser($user)->allows('manage-workspace-billing', $billingOwner);
        $subscription = $billingOwner->subscription('default');
        $currentPlan = $this->currentPlan($billingOwner, $subscription);
        $status = $subscription?->stripe_status ?? ($billingOwner->plan ?: 'none');
        $subscriptionStatus = SubscriptionStatus::tryFrom((string) $status);

        return [
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
        ];
    }

    private function currentPlan(User $user, mixed $subscription): ?Plan
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
