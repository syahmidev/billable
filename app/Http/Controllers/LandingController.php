<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function __invoke(): Response
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('price')
            ->get(['name', 'slug', 'price', 'features']);

        return Inertia::render('Landing', [
            'plans' => $plans,
        ]);
    }
}
