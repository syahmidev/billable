<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceMail extends Mailable
{
    public function __construct(
        public Invoice $invoice,
        public string $workspaceName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->invoice->invoice_number} from {$this->workspaceName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'workspaceName' => $this->workspaceName,
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
