<?php

declare(strict_types=1);

use App\Http\Controllers\Payment\InvoicePaymentController;
use App\Http\Controllers\Tenant\ActivityLogController;
use App\Http\Controllers\Tenant\BillingController;
use App\Http\Controllers\Tenant\ClientController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\TeamMemberController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.active',
])->group(function (): void {
    // Public invoice payment pages — no auth required
    Route::get('/pay/{token}', [InvoicePaymentController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('tenant.invoice.pay');
    Route::post('/pay/{token}/intent', [InvoicePaymentController::class, 'createIntent'])
        ->middleware('throttle:10,1')
        ->name('tenant.invoice.intent');

    Route::middleware(['auth', 'tenant.member'])->group(function (): void {
        Route::get('/billing', [BillingController::class, 'index'])->name('tenant.billing.index');
        Route::post('/billing/plans/{plan}/subscribe', [BillingController::class, 'subscribe'])->name('tenant.billing.subscribe');
        Route::get('/billing/portal', [BillingController::class, 'portal'])->name('tenant.billing.portal');

        Route::middleware('subscribed')->group(function (): void {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
            Route::get('/activity', [ActivityLogController::class, 'index'])->name('tenant.activity.index');

            Route::get('/team', [TeamMemberController::class, 'index'])->name('tenant.team.index');
            Route::post('/team', [TeamMemberController::class, 'store'])->name('tenant.team.store');
            Route::put('/team/{member}', [TeamMemberController::class, 'update'])->name('tenant.team.update');
            Route::delete('/team/{member}', [TeamMemberController::class, 'destroy'])->name('tenant.team.destroy');

            Route::resource('clients', ClientController::class)->names([
                'index' => 'tenant.clients.index',
                'create' => 'tenant.clients.create',
                'store' => 'tenant.clients.store',
                'show' => 'tenant.clients.show',
                'edit' => 'tenant.clients.edit',
                'update' => 'tenant.clients.update',
                'destroy' => 'tenant.clients.destroy',
            ]);

            Route::get('invoices/export', [InvoiceController::class, 'export'])->middleware('throttle:10,1')->name('tenant.invoices.export');
            Route::resource('invoices', InvoiceController::class)->names([
                'index' => 'tenant.invoices.index',
                'create' => 'tenant.invoices.create',
                'store' => 'tenant.invoices.store',
                'show' => 'tenant.invoices.show',
                'edit' => 'tenant.invoices.edit',
                'update' => 'tenant.invoices.update',
                'destroy' => 'tenant.invoices.destroy',
            ]);
            Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('tenant.invoices.send');
            Route::post('invoices/{invoice}/remind', [InvoiceController::class, 'remind'])->name('tenant.invoices.remind');
            Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->middleware('throttle:20,1')->name('tenant.invoices.pdf');
        });
    });
});
