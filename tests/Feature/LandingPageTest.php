<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PlanSlug;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_active_plans(): void
    {
        Plan::create([
            'name' => 'Free',
            'slug' => PlanSlug::Free->value,
            'stripe_price_id' => 'price_free',
            'price' => 0,
            'features' => ['3 clients'],
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Hidden',
            'slug' => 'hidden',
            'stripe_price_id' => 'price_hidden',
            'price' => 999,
            'features' => ['Internal plan'],
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Landing')
                ->has('plans', 1)
                ->where('plans.0.slug', PlanSlug::Free->value)
                ->where('plans.0.name', 'Free'));
    }
}
