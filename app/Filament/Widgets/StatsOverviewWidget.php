<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PlanSlug;
use App\Models\Tenant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeSubs = Subscription::where('stripe_status', 'active')->count();

        $mrr = (int) DB::connection('pgsql')
            ->table('users')
            ->whereNotNull('plan')
            ->where('plan', '!=', PlanSlug::Free->value)
            ->join('plans', 'users.plan', '=', 'plans.slug')
            ->sum('plans.price');

        $totalUsers = User::where('is_admin', false)->count();

        return [
            Stat::make('Total Workspaces', $totalTenants)
                ->description('Registered tenants')
                ->color('primary')
                ->icon('heroicon-o-building-office'),

            Stat::make('Active Subscriptions', $activeSubs)
                ->description('Paying customers')
                ->color('success')
                ->icon('heroicon-o-credit-card'),

            Stat::make('MRR', '$'.number_format($mrr))
                ->description('Monthly recurring revenue')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Users', $totalUsers)
                ->description('Registered accounts')
                ->color('info')
                ->icon('heroicon-o-users'),
        ];
    }
}
