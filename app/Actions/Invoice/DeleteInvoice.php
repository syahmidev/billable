<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Actions\Activity\RecordActivity;
use App\Enums\ActivityType;
use App\Models\Invoice;
use App\Models\User;

class DeleteInvoice
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function handle(Invoice $invoice, ?User $actor = null): void
    {
        $invoiceNumber = $invoice->invoice_number;
        $invoiceId = $invoice->id;

        $invoice->delete();

        $this->activity->handle(
            type: ActivityType::InvoiceDeleted,
            description: "{$invoiceNumber} was deleted.",
            actor: $actor,
            metadata: ['invoice_id' => $invoiceId],
        );
    }
}
