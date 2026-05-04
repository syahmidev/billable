<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\CreateWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OnboardingController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        if ($url = $request->user()->tenantUrl()) {
            return redirect()->away($url);
        }

        return Inertia::render('Onboarding/Index');
    }

    public function store(Request $request, CreateWorkspace $action): RedirectResponse|SymfonyResponse
    {
        $request->validate([
            'workspace_name' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$|^[a-z0-9]$/'],
        ]);

        if ($action->isSubdomainTaken($request->subdomain)) {
            return back()->withErrors(['subdomain' => 'This subdomain is not available.']);
        }

        $url = $action->handle($request->user(), $request->workspace_name, $request->subdomain);

        return Inertia::location($url);
    }
}
