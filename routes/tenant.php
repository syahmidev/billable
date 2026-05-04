<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\ClientController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\InvoiceController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::middleware(['auth', 'tenant.member', 'subscribed'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        Route::resource('clients', ClientController::class)->names([
            'index' => 'tenant.clients.index',
            'create' => 'tenant.clients.create',
            'store' => 'tenant.clients.store',
            'show' => 'tenant.clients.show',
            'edit' => 'tenant.clients.edit',
            'update' => 'tenant.clients.update',
            'destroy' => 'tenant.clients.destroy',
        ]);

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
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('tenant.invoices.pdf');
    });
});
