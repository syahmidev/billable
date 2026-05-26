<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Billing\PlanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Seo\RobotsController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\Stripe\InvoiceWebhookController;
use App\Http\Middleware\EnsureCashierWebhookSecretIsConfigured;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    Route::post('/plans/{plan}/subscribe', [PlanController::class, 'subscribe'])->name('plans.subscribe');
    Route::get('/billing/success', [PlanController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [PlanController::class, 'portal'])->name('billing.portal');
});

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])
    ->middleware(EnsureCashierWebhookSecretIsConfigured::class)
    ->name('cashier.webhook');

Route::post('/stripe/invoice-webhook', [InvoiceWebhookController::class, 'handle'])
    ->name('stripe.invoice.webhook');

Route::get('/', LandingController::class)->name('landing');
