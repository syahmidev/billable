<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 40px; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .brand { font-size: 22px; font-weight: 700; color: #7c3aed; letter-spacing: -0.5px; }
        .workspace { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-draft { background: #f3f4f6; color: #6b7280; }
        .badge-sent { background: #dbeafe; color: #1d4ed8; }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-overdue { background: #fee2e2; color: #dc2626; }
        .badge-cancelled { background: #f3f4f6; color: #6b7280; }
        .invoice-title { font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .invoice-number { font-size: 13px; color: #6b7280; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .parties { display: table; width: 100%; margin-bottom: 32px; }
        .party { display: table-cell; width: 50%; vertical-align: top; }
        .party-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .party-name { font-size: 14px; font-weight: 600; color: #111827; }
        .party-detail { font-size: 11px; color: #6b7280; margin-top: 2px; line-height: 1.5; }
        .meta { display: table; width: 100%; margin-bottom: 32px; }
        .meta-item { display: table-cell; width: 33%; vertical-align: top; }
        .meta-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 4px; }
        .meta-value { font-size: 12px; color: #111827; font-weight: 500; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.items thead tr { background: #f9fafb; }
        table.items th { padding: 9px 12px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        table.items th.right { text-align: right; }
        table.items td { padding: 10px 12px; font-size: 11px; color: #374151; border-bottom: 1px solid #f3f4f6; }
        table.items td.right { text-align: right; }
        table.items td.muted { color: #6b7280; }
        .totals { width: 240px; margin-left: auto; }
        .totals-row { display: table; width: 100%; padding: 5px 0; }
        .totals-label { display: table-cell; font-size: 11px; color: #6b7280; }
        .totals-value { display: table-cell; font-size: 11px; color: #111827; text-align: right; }
        .totals-divider { border: none; border-top: 1px solid #e5e7eb; margin: 8px 0; }
        .totals-total .totals-label { font-size: 13px; font-weight: 700; color: #111827; }
        .totals-total .totals-value { font-size: 16px; font-weight: 700; color: #7c3aed; }
        .notes-section { margin-top: 32px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .notes-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .notes-text { font-size: 11px; color: #6b7280; line-height: 1.6; }
        .footer { margin-top: 48px; padding-top: 16px; border-top: 1px solid #f3f4f6; text-align: center; font-size: 10px; color: #d1d5db; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="brand">bill<span style="color:#7c3aed">able</span></div>
            <div class="workspace">{{ $workspaceName }}</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">Invoice</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div style="margin-top:8px">
                <span class="badge badge-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <div class="party-label">From</div>
            <div class="party-name">{{ $workspaceName }}</div>
        </div>
        <div class="party">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $invoice->client->name }}</div>
            @if($invoice->client->company)
                <div class="party-detail">{{ $invoice->client->company }}</div>
            @endif
            @if($invoice->client->email)
                <div class="party-detail">{{ $invoice->client->email }}</div>
            @endif
            @if($invoice->client->address)
                <div class="party-detail">{{ $invoice->client->address }}</div>
            @endif
        </div>
    </div>

    <div class="meta">
        <div class="meta-item">
            <div class="meta-label">Invoice #</div>
            <div class="meta-value">{{ $invoice->invoice_number }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Issue Date</div>
            <div class="meta-value">{{ $invoice->issue_date->format('M d, Y') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Due Date</div>
            <div class="meta-value">{{ $invoice->due_date->format('M d, Y') }}</div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="right" style="width:80px">Qty</th>
                <th class="right" style="width:100px">Unit Price</th>
                <th class="right" style="width:100px">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="right muted">{{ number_format((float)$item->quantity, 2) }}</td>
                <td class="right muted">${{ number_format((float)$item->unit_price, 2) }}</td>
                <td class="right">${{ number_format((float)$item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="totals-label">Subtotal</div>
            <div class="totals-value">${{ number_format((float)$invoice->subtotal, 2) }}</div>
        </div>
        @if((float)$invoice->discount_percent > 0)
        <div class="totals-row">
            <div class="totals-label">Discount ({{ (float)$invoice->discount_percent }}%)</div>
            <div class="totals-value">-${{ number_format($invoice->discountAmount(), 2) }}</div>
        </div>
        @endif
        @if((float)$invoice->tax_percent > 0)
        <div class="totals-row">
            <div class="totals-label">Tax ({{ (float)$invoice->tax_percent }}%)</div>
            <div class="totals-value">${{ number_format($invoice->taxAmount(), 2) }}</div>
        </div>
        @endif
        <hr class="totals-divider">
        <div class="totals-row totals-total">
            <div class="totals-label">Total Due</div>
            <div class="totals-value">${{ number_format((float)$invoice->total, 2) }}</div>
        </div>
    </div>

    @if($invoice->notes)
    <div class="notes-section">
        <div class="notes-label">Notes</div>
        <div class="notes-text">{{ $invoice->notes }}</div>
    </div>
    @endif

    <div class="footer">Generated by billable · Thank you for your business</div>
</body>
</html>
