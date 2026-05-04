<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Billing\PlanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Seo\RobotsController;
use App\Http\Controllers\Seo\SitemapController;
use App\Http\Controllers\Stripe\InvoiceWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Controllers\WebhookController;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    Route::post('/plans/{plan}/subscribe', [PlanController::class, 'subscribe'])->name('plans.subscribe');
    Route::get('/billing/success', [PlanController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [PlanController::class, 'portal'])->name('billing.portal');
});

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

Route::post('/stripe/invoice-webhook', [InvoiceWebhookController::class, 'handle'])
    ->name('stripe.invoice.webhook');

Route::get('/', LandingController::class)->name('landing');
