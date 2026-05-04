<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 14px; color: #374151; background: #f9fafb; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 12px; padding: 36px; border: 1px solid #e5e7eb; }
        .brand { font-size: 20px; font-weight: 700; color: #7c3aed; margin-bottom: 24px; }
        h2 { font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 8px; }
        p { margin: 0 0 16px; line-height: 1.6; color: #6b7280; }
        .amount { font-size: 28px; font-weight: 700; color: #111827; margin: 24px 0; }
        .meta { background: #f9fafb; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .meta-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .meta-label { color: #9ca3af; }
        .meta-value { color: #111827; font-weight: 500; }
        .footer { margin-top: 24px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="brand">billable</div>

            <h2>You have a new invoice from {{ $workspaceName }}</h2>
            <p>Hi {{ $invoice->client->name }}, please find your invoice attached to this email.</p>

            <div class="amount">${{ number_format((float)$invoice->total, 2) }}</div>

            <div class="meta">
                <div class="meta-row">
                    <span class="meta-label">Invoice</span>
                    <span class="meta-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Issue Date</span>
                    <span class="meta-value">{{ $invoice->issue_date->format('M d, Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Due Date</span>
                    <span class="meta-value">{{ $invoice->due_date->format('M d, Y') }}</span>
                </div>
            </div>

            <p>The invoice PDF is attached to this email for your records.</p>

            @if($invoice->notes)
            <p style="font-size:13px; padding: 12px; background: #f3f4f6; border-radius: 6px; color: #6b7280;">
                {{ $invoice->notes }}
            </p>
            @endif
        </div>
        <div class="footer">Sent via billable · {{ $workspaceName }}</div>
    </div>
</body>
</html>
