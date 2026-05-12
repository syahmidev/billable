<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PlanSlug;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $stripePriceIds = [
            PlanSlug::Pro->value => config('billing.stripe_prices.pro'),
            PlanSlug::Business->value => config('billing.stripe_prices.business'),
        ];

        $plans = [
            [
                'name' => 'Free',
                'slug' => PlanSlug::Free->value,
                'price' => 0,
                'features' => [
                    'Up to 3 clients',
                    'Up to 5 invoices/month',
                    'Basic invoice templates',
                    'Email delivery',
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => PlanSlug::Pro->value,
                'price' => 29,
                'features' => [
                    'Unlimited clients',
                    'Unlimited invoices',
                    'Custom branding',
                    'PDF generation',
                    'Online payments',
                    'Email delivery',
                ],
            ],
            [
                'name' => 'Business',
                'slug' => PlanSlug::Business->value,
                'price' => 79,
                'features' => [
                    'Everything in Pro',
                    'Multiple team members',
                    'Advanced analytics',
                    'Priority support',
                    'Custom domain',
                    'API access',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $stripePriceId = $stripePriceIds[$plan['slug']] ?? null;

            if ($plan['slug'] === PlanSlug::Free->value) {
                $plan['stripe_price_id'] = null;
            } elseif (is_string($stripePriceId) && $stripePriceId !== '') {
                $plan['stripe_price_id'] = $stripePriceId;
            }

            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
