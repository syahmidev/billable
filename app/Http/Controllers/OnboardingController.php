<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\CreateWorkspace;
use App\Http\Requests\StoreOnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        if ($url = $request->user()->tenantUrl()) {
            return redirect()->away($url);
        }

        return Inertia::render('Onboarding/Index');
    }

    public function store(StoreOnboardingRequest $request, CreateWorkspace $action): RedirectResponse
    {
        $data = $request->validated();

        $action->handle($request->user(), $data['workspace_name'], $data['subdomain']);

        return redirect()->route('plans');
    }
}
