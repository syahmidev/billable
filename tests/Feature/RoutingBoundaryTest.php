<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutingBoundaryTest extends TestCase
{
    public function test_dashboard_is_only_registered_as_a_tenant_route(): void
    {
        $route = Route::getRoutes()->getByName('tenant.dashboard');

        $this->assertNotNull($route);
        $this->assertSame('dashboard', $route->uri());
        $this->assertNull(Route::getRoutes()->getByName('dashboard'));
    }

    public function test_billing_index_is_only_registered_as_a_tenant_route(): void
    {
        $route = Route::getRoutes()->getByName('tenant.billing.index');

        $this->assertNotNull($route);
        $this->assertSame('billing', $route->uri());
        $this->assertNull(Route::getRoutes()->getByName('billing.index'));
    }
}
