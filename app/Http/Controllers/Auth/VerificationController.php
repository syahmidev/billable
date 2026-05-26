<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerificationController extends Controller
{
    public function showNotice(Request $request): Response|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('onboarding'));
        }

        return Inertia::render('Auth/VerifyEmail', [
            'email' => $request->user()->email,
        ]);
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('onboarding'));
        }

        $request->fulfill();

        event(new Verified($request->user()));

        return redirect()->intended(route('onboarding'))
            ->with('success', 'Email verified! Welcome to billable.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('onboarding'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent! Check your inbox.');
    }
}
