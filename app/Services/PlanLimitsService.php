<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PlanSlug;
use App\Enums\UserRole;
use App\Exceptions\PlanLimitExceededException;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PlanLimitsService
{
    private const array LIMITS = [
        PlanSlug::Free->value => [
            'clients' => 3,
            'invoices_per_month' => 5,
        ],
    ];

    public function enforceClientLimit(): void
    {
        $limit = $this->clientLimit();

        if ($limit === null) {
            return;
        }

        if (Client::count() >= $limit) {
            throw PlanLimitExceededException::clients($limit);
        }
    }

    public function enforceInvoiceLimit(): void
    {
        $limit = $this->invoiceMonthlyLimit();

        if ($limit === null) {
            return;
        }

        $count = Invoice::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        if ($count >= $limit) {
            throw PlanLimitExceededException::invoices($limit);
        }
    }

    private function clientLimit(): ?int
    {
        return self::LIMITS[$this->ownerPlan()]['clients'] ?? null;
    }

    private function invoiceMonthlyLimit(): ?int
    {
        return self::LIMITS[$this->ownerPlan()]['invoices_per_month'] ?? null;
    }

    private function ownerPlan(): string
    {
        return Cache::remember(
            'tenant_owner_plan_'.tenant('id'),
            now()->addMinutes(5),
            fn (): string => User::query()
                ->where('tenant_id', tenant('id'))
                ->where('role', UserRole::Owner->value)
                ->oldest('id')
                ->value('plan') ?? PlanSlug::Free->value
        );
    }
}
