<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PlanSlug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RegisterPlanIntentTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_accepts_valid_plan_intent(): void
    {
        $this->get('/register?plan=pro')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Auth/Register')
                ->where('selectedPlan', PlanSlug::Pro->value));
    }

    public function test_register_page_ignores_invalid_plan_intent(): void
    {
        $this->get('/register?plan=enterprise')
            ->assertOk()
            ->assertInertia(fn (Assert $page): Assert => $page
                ->component('Auth/Register')
                ->where('selectedPlan', null));
    }

    public function test_registration_preserves_plan_intent_in_session(): void
    {
        $response = $this->post('/register', [
            'name' => 'Miyu Studio',
            'email' => 'miyu@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plan' => PlanSlug::Business->value,
        ]);

        $response
            ->assertRedirect(route('onboarding'))
            ->assertSessionHas('intended_plan', PlanSlug::Business->value);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'miyu@example.test',
        ]);
    }
}
