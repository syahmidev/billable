<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 14px; color: #374151; background: #f9fafb; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .card { background: #fff; border-radius: 12px; padding: 36px; border: 1px solid #e5e7eb; }
        .brand { font-size: 20px; font-weight: 700; color: #7c3aed; margin-bottom: 24px; }
        .checkmark { width: 48px; height: 48px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        h2 { font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 8px; text-align: center; }
        p { margin: 0 0 16px; line-height: 1.6; color: #6b7280; text-align: center; }
        .amount { font-size: 32px; font-weight: 700; color: #15803d; text-align: center; margin: 20px 0; }
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
            <div class="brand" style="text-align:center">billable</div>

            <div style="text-align:center; margin-bottom:16px">
                <div style="width:48px; height:48px; background:#dcfce7; border-radius:50%; display:inline-flex; align-items:center; justify-content:center">
                    <span style="color:#15803d; font-size:22px">✓</span>
                </div>
            </div>

            <h2>Payment Received!</h2>
            <p>Hi {{ $invoice->client->name }}, your payment has been successfully processed.</p>

            <div class="amount">${{ number_format((float)$invoice->total, 2) }}</div>

            <div class="meta">
                <div class="meta-row">
                    <span class="meta-label">Invoice</span>
                    <span class="meta-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">From</span>
                    <span class="meta-value">{{ $workspaceName }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Paid on</span>
                    <span class="meta-value">{{ $invoice->paid_at?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status</span>
                    <span class="meta-value" style="color:#15803d; font-weight:600">Paid</span>
                </div>
            </div>

            <p style="font-size:13px">Keep this email as your payment confirmation. Thank you for your business!</p>
        </div>
        <div class="footer">Powered by billable · {{ $workspaceName }}</div>
    </div>
</body>
</html>
