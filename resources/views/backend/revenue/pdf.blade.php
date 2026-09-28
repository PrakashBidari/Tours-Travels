<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 0; }
        p.period { color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 10px; text-align: left; }
        th { background: #f9fafb; }
        .totals { margin-top: 16px; }
        .totals td { border: none; padding: 2px 0; }
    </style>
</head>
<body>
    <h1>Revenue Report</h1>
    <p class="period">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>

    <table class="totals">
        <tr><td><strong>Total bookings:</strong></td><td>{{ $totals['bookings_count'] }}</td></tr>
        <tr><td><strong>Total platform revenue:</strong></td><td>${{ number_format($totals['admin_revenue'], 2) }}</td></tr>
        <tr><td><strong>Total vendor payouts:</strong></td><td>${{ number_format($totals['vendor_payout'], 2) }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Bookings</th>
                <th>Platform Revenue</th>
                <th>Vendor Payout</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revenueByVendor as $row)
                <tr>
                    <td>{{ $row->vendor?->name ?? 'Unassigned' }}</td>
                    <td>{{ $row->bookings_count }}</td>
                    <td>${{ number_format($row->admin_revenue, 2) }}</td>
                    <td>${{ number_format($row->vendor_payout, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No revenue in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
