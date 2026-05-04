<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\RegisterUser;
use App\Enums\PlanSlug;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends Controller
{
    public function showRegister(Request $request): Response
    {
        $selectedPlan = PlanSlug::tryFrom((string) $request->query('plan'))?->value;

        return Inertia::render('Auth/Register', [
            'selectedPlan' => $selectedPlan,
        ]);
    }

    public function register(RegisterRequest $request, RegisterUser $action): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['plan'])) {
            $request->session()->put('intended_plan', $data['plan']);
        }

        $action->handle($data['name'], $data['email'], $data['password']);

        return redirect()->route('onboarding');
    }

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request, LoginUser $action): RedirectResponse|SymfonyResponse
    {
        $data = $request->validated();

        if (! $action->handle($request, $data['email'], $data['password'], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if ($url = $user->tenantUrl()) {
            return Inertia::location($url);
        }

        return redirect()->route('onboarding');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
