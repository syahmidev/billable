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

    public function test_tenant_billing_routes_remain_available_for_subscription_recovery(): void
    {
        $billingRoute = Route::getRoutes()->getByName('tenant.billing.index');
        $dashboardRoute = Route::getRoutes()->getByName('tenant.dashboard');

        $this->assertNotNull($billingRoute);
        $this->assertNotNull($dashboardRoute);
        $this->assertNotContains('subscribed', $billingRoute->gatherMiddleware());
        $this->assertContains('subscribed', $dashboardRoute->gatherMiddleware());
    }

    public function test_team_activity_and_invoice_reminder_routes_are_registered(): void
    {
        $this->assertSame('team', Route::getRoutes()->getByName('tenant.team.index')?->uri());
        $this->assertSame('activity', Route::getRoutes()->getByName('tenant.activity.index')?->uri());
        $this->assertSame('invoices/{invoice}/remind', Route::getRoutes()->getByName('tenant.invoices.remind')?->uri());
    }

    public function test_public_invoice_payment_routes_are_throttled(): void
    {
        $paymentPageRoute = Route::getRoutes()->getByName('tenant.invoice.pay');
        $paymentIntentRoute = Route::getRoutes()->getByName('tenant.invoice.intent');

        $this->assertNotNull($paymentPageRoute);
        $this->assertNotNull($paymentIntentRoute);
        $this->assertContains('throttle:60,1', $paymentPageRoute->gatherMiddleware());
        $this->assertContains('throttle:10,1', $paymentIntentRoute->gatherMiddleware());
    }

    public function test_pdf_download_route_is_throttled(): void
    {
        $route = Route::getRoutes()->getByName('tenant.invoices.pdf');

        $this->assertNotNull($route);
        $this->assertContains('throttle:20,1', $route->gatherMiddleware());
    }

    public function test_invoice_export_route_is_registered(): void
    {
        $route = Route::getRoutes()->getByName('tenant.invoices.export');

        $this->assertNotNull($route);
        $this->assertSame('invoices/export', $route->uri());
        $this->assertContains('GET', $route->methods());
    }
}
