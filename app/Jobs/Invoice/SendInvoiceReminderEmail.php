<?php

declare(strict_types=1);

namespace App\Jobs\Invoice;

use App\Mail\InvoiceReminderMail;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminderEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        private readonly int $invoiceId,
        private readonly string $recipientEmail,
        private readonly string $workspaceName,
    ) {}

    public function handle(): void
    {
        $invoice = Invoice::with('client', 'items')->findOrFail($this->invoiceId);

        Mail::to($this->recipientEmail)
            ->send(new InvoiceReminderMail($invoice, $this->workspaceName));
    }
}
