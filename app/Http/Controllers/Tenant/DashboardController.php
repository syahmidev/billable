<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\ViewModels\Tenant\DashboardViewModel;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardViewModel $dashboard): Response
    {
        return Inertia::render('Tenant/Dashboard', $dashboard->toArray());
    }
}
