<?php

declare(strict_types=1);

namespace App\ViewModels\Tenant;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Collection;

class DashboardViewModel
{
    public function toArray(): array
    {
        return [
            'workspace' => [
                'name' => tenant('name'),
                'id' => tenant('id'),
            ],
            'stats' => $this->stats(),
            'chartData' => $this->chartData(),
            'recentInvoices' => $this->recentInvoices(),
            'recentActivity' => $this->recentActivity(),
        ];
    }

    private function stats(): array
    {
        return [
            'total_revenue' => (float) Invoice::where('status', Invoice::STATUS_PAID)->sum('total'),
            'outstanding' => (float) Invoice::where('status', Invoice::STATUS_SENT)->sum('total'),
            'outstanding_count' => Invoice::where('status', Invoice::STATUS_SENT)->count(),
            'overdue' => (float) Invoice::where('status', Invoice::STATUS_OVERDUE)->sum('total'),
            'overdue_count' => Invoice::where('status', Invoice::STATUS_OVERDUE)->count(),
            'client_count' => Client::count(),
            'draft_count' => Invoice::where('status', Invoice::STATUS_DRAFT)->count(),
        ];
    }

    private function chartData(): array
    {
        $now = now();
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);

            $chartData[] = [
                'label' => $date->format('M'),
                'revenue' => (float) Invoice::where('status', Invoice::STATUS_PAID)
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('total'),
            ];
        }

        return $chartData;
    }

    private function recentInvoices(): Collection
    {
        return Invoice::with('client')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Invoice $invoice): array => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'total' => (float) $invoice->total,
                'issue_date' => $invoice->issue_date->format('M d, Y'),
                'client_name' => $invoice->client?->name ?? '-',
            ]);
    }

    private function recentActivity(): Collection
    {
        return ActivityLog::query()
            ->latest('occurred_at')
            ->limit(5)
            ->get()
            ->map(fn (ActivityLog $activity): array => [
                'id' => $activity->id,
                'label' => $activity->type->label(),
                'description' => $activity->description,
                'actor_name' => $activity->actor_name,
                'occurred_at' => $activity->occurred_at?->diffForHumans(),
            ]);
    }
}
