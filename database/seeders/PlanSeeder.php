<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'stripe_price_id' => 'price_1TTGRPF0HPxuwMFJEqNSURFQ',
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
                'slug' => 'pro',
                'stripe_price_id' => 'price_1TTGRnF0HPxuwMFJ0vR15Jip',
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
                'slug' => 'business',
                'stripe_price_id' => 'price_1TTGS6F0HPxuwMFJNYAoAwYP',
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
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
