<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentReceiptMail extends Mailable
{
    public function __construct(
        public Invoice $invoice,
        public string $workspaceName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Received — {$this->invoice->invoice_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-receipt',
        );
    }
}
