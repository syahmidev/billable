<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Stats
        $totalRevenue = (float) Invoice::where('status', Invoice::STATUS_PAID)->sum('total');
        $outstanding = (float) Invoice::where('status', Invoice::STATUS_SENT)->sum('total');
        $outstandingCount = Invoice::where('status', Invoice::STATUS_SENT)->count();
        $overdue = (float) Invoice::where('status', Invoice::STATUS_OVERDUE)->sum('total');
        $overdueCount = Invoice::where('status', Invoice::STATUS_OVERDUE)->count();
        $clientCount = Client::count();
        $draftCount = Invoice::where('status', Invoice::STATUS_DRAFT)->count();

        // Revenue chart — last 6 months (paid invoices grouped by month)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = (float) Invoice::where('status', Invoice::STATUS_PAID)
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('total');
            $chartData[] = [
                'label' => $date->format('M'),
                'revenue' => $revenue,
            ];
        }

        // Recent invoices
        $recentInvoices = Invoice::with('client')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($inv) => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'status' => $inv->status,
                'total' => (float) $inv->total,
                'issue_date' => $inv->issue_date->format('M d, Y'),
                'client_name' => $inv->client?->name ?? '—',
            ]);

        return Inertia::render('Tenant/Dashboard', [
            'workspace' => [
                'name' => tenant('name'),
                'id' => tenant('id'),
            ],
            'stats' => [
                'total_revenue' => $totalRevenue,
                'outstanding' => $outstanding,
                'outstanding_count' => $outstandingCount,
                'overdue' => $overdue,
                'overdue_count' => $overdueCount,
                'client_count' => $clientCount,
                'draft_count' => $draftCount,
            ],
            'chart_data' => $chartData,
            'recent_invoices' => $recentInvoices,
        ]);
    }
}
