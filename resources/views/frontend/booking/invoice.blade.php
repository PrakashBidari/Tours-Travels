<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
        .top { background: #0047ab; color: #fff; padding: 22px 30px; }
        .top table { width: 100%; }
        .brand { font-size: 18px; font-weight: bold; }
        .brand span { color: #f4b400; }
        .muted { color: #d9e8ff; font-size: 10px; }
        .title { font-size: 22px; font-weight: bold; text-align: right; color: #f4b400; }
        .content { padding: 24px 30px; }
        table.meta { width: 100%; margin-bottom: 18px; }
        table.meta td { vertical-align: top; width: 50%; }
        .label { color: #64748b; font-size: 9px; text-transform: uppercase; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th { background: #eef5ff; color: #0b1f47; text-align: left; padding: 8px; font-size: 10px; }
        table.items td { padding: 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .right { text-align: right; }
        .totals { width: 45%; margin-left: 55%; margin-top: 12px; border-collapse: collapse; }
        .totals td { padding: 5px 8px; }
        .totals .grand td { border-top: 2px solid #0047ab; font-size: 14px; font-weight: bold; color: #0047ab; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .paid { background: #d1fae5; color: #047857; }
        .unpaid { background: #fef3c7; color: #b45309; }
        .details td { padding: 3px 0; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="top">
        <table><tr>
            <td>
                <div class="brand">RAM TOURS <span>&amp;</span> TRAVEL PVT. LTD.</div>
                <div class="muted">{{ site('address') }}<br>{{ site('phone') }} · {{ site('email') }}<br>{{ config('travel.company.registration') }}</div>
            </td>
            <td class="title">INVOICE</td>
        </tr></table>
    </div>

    <div class="content">
        <table class="meta"><tr>
            <td>
                <div class="label">Billed to</div>
                <strong>{{ $booking->full_name }}</strong><br>
                {{ $booking->email }}<br>{{ $booking->phone }}
                @if ($booking->nationality)<br>{{ $booking->nationality }}@endif
            </td>
            <td class="right">
                <div class="label">Invoice / Booking ID</div><strong>{{ $booking->reference }}</strong><br>
                <div class="label" style="margin-top:6px">Date</div>{{ $booking->created_at->format('M d, Y') }}<br>
                <div style="margin-top:6px">
                    <span class="badge {{ $booking->isPaid() ? 'paid' : 'unpaid' }}">{{ strtoupper($booking->payment_status) }}</span>
                    <span class="badge unpaid" style="background:#e0f2fe;color:#0369a1">{{ strtoupper($booking->status) }}</span>
                </div>
            </td>
        </tr></table>

        <table class="items">
            <thead><tr><th>Description</th><th class="right">Unit price</th><th class="right">Qty</th><th class="right">Amount</th></tr></thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $booking->service_label }}: {{ $booking->title }}</strong>
                        <table class="details">
                            @if ($booking->travel_date)<tr><td>Travel date: {{ $booking->travel_date->format('M d, Y') }}{{ $booking->return_date ? ' – '.$booking->return_date->format('M d, Y') : '' }}</td></tr>@endif
                            @foreach (collect($booking->details ?? [])->except('documents')->filter(fn ($v) => filled($v)) as $key => $value)
                                <tr><td>{{ Str::headline($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}</td></tr>
                            @endforeach
                            @if ($booking->pnr)<tr><td>PNR: {{ $booking->pnr }}</td></tr>@endif
                            @if ($booking->ticket_number)<tr><td>Ticket no.: {{ $booking->ticket_number }}</td></tr>@endif
                        </table>
                    </td>
                    <td class="right">{{ $booking->unit_price > 0 ? npr($booking->unit_price, true) : 'To be quoted' }}</td>
                    <td class="right">{{ $booking->quantity }}</td>
                    <td class="right">{{ npr($booking->subtotal, true) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="totals">
            <tr><td>Subtotal</td><td class="right">{{ npr($booking->subtotal, true) }}</td></tr>
            @if ($booking->discount > 0)<tr><td>Discount {{ $booking->coupon_code ? '('.$booking->coupon_code.')' : '' }}</td><td class="right">- {{ npr($booking->discount, true) }}</td></tr>@endif
            <tr class="grand"><td>Total (NPR)</td><td class="right">{{ npr($booking->total, true) }}</td></tr>
            @if ($booking->isPaid())<tr><td colspan="2" class="right" style="color:#047857">Paid via {{ $booking->payment_method_label }} on {{ $booking->paid_at?->format('M d, Y') }}{{ $booking->transaction_id ? ' · Txn '.$booking->transaction_id : '' }}</td></tr>@endif
        </table>

        <table style="width:100%; margin-top: 24px"><tr>
            <td style="width:120px"><img src="{{ $qr }}" width="110" height="110" alt="QR"></td>
            <td style="vertical-align:middle; color:#64748b; font-size:10px">
                Scan to view your live booking status.<br>
                Please carry this invoice and a valid photo ID / passport while travelling.<br>
                Cancellations and refunds follow our Refund Policy and the supplier's terms.
            </td>
        </tr></table>

        <div class="footer">
            This is a computer-generated invoice and does not require a signature. Thank you for travelling with {{ site('name') }}.
        </div>
    </div>
</body>
</html>
