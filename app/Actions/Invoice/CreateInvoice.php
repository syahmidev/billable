<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceNumberService;
use App\Services\PlanLimitsService;
use App\Support\InvoiceTotals;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateInvoice
{
    public function __construct(
        private readonly RecordActivity $activity,
        private readonly InvoiceNumberService $invoiceNumbers,
        private readonly PlanLimitsService $planLimits,
    ) {}

    public function handle(array $data, ?User $actor = null): Invoice
    {
        $this->planLimits->enforceInvoiceLimit();

        $totals = InvoiceTotals::fromItems(
            $data['items'],
            $data['discount_percent'] ?? 0,
            $data['tax_percent'] ?? 0,
        );

        return DB::transaction(function () use ($data, $actor, $totals): Invoice {
            $invoice = Invoice::create([
                'invoice_number' => $this->invoiceNumbers->next(),
                'client_id' => $data['client_id'],
                'status' => InvoiceStatus::Draft->value,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'subtotal' => $totals->subtotal,
                'discount_percent' => $data['discount_percent'] ?? 0,
                'tax_percent' => $data['tax_percent'] ?? 0,
                'total' => $totals->total,
                'notes' => $data['notes'] ?? null,
                'payment_token' => (string) Str::uuid(),
            ]);

            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => InvoiceTotals::lineTotal($item),
                ]);
            }

            $this->activity->handle(
                type: ActivityType::InvoiceCreated,
                description: "{$invoice->invoice_number} was created.",
                actor: $actor,
                subject: $invoice,
                metadata: ['total' => (float) $invoice->total],
            );

            return $invoice;
        });
    }
}
