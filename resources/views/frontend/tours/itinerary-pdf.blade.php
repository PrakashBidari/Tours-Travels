<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tour->title }} — Itinerary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; margin: 0; }
        .header { background: #0047ab; color: #fff; padding: 22px 28px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #d9e8ff; }
        .brand { font-size: 13px; font-weight: bold; color: #f4b400; letter-spacing: 1px; }
        .content { padding: 20px 28px; }
        h2 { font-size: 13px; color: #0047ab; border-bottom: 2px solid #f4b400; padding-bottom: 4px; margin: 18px 0 8px; }
        table.facts { width: 100%; border-collapse: collapse; }
        table.facts td { padding: 6px 8px; border: 1px solid #e2e8f0; width: 25%; vertical-align: top; }
        table.facts .k { color: #64748b; font-size: 9px; text-transform: uppercase; display: block; }
        .day { margin-bottom: 8px; padding: 8px 10px; border-left: 3px solid #00aeef; background: #f8fafc; }
        .day strong { color: #0b1f47; }
        ul { margin: 0; padding-left: 16px; }
        li { margin-bottom: 3px; }
        .cols td { width: 50%; vertical-align: top; padding-right: 12px; }
        .price { font-size: 16px; font-weight: bold; color: #0047ab; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">RAM TOURS &amp; TRAVEL PVT. LTD.</div>
        <h1>{{ $tour->title }}</h1>
        <p>{{ $tour->destination }}, {{ $tour->country }} · {{ $tour->duration_label }} · {{ $tour->category_label }}</p>
    </div>

    <div class="content">
        <table class="facts">
            <tr>
                <td><span class="k">Price per person</span><span class="price">{{ npr($tour->final_price) }}</span></td>
                <td><span class="k">Duration</span>{{ $tour->duration_label }}</td>
                <td><span class="k">Best season</span>{{ $tour->season_label }}</td>
                <td><span class="k">Group size</span>{{ $tour->group_size ?: '—' }}</td>
            </tr>
            <tr>
                <td><span class="k">Accommodation</span>{{ $tour->hotel ?: '—' }}</td>
                <td><span class="k">Meals</span>{{ $tour->meals ?: '—' }}</td>
                <td><span class="k">Transport</span>{{ $tour->transport ?: '—' }}</td>
                <td><span class="k">{{ $tour->max_altitude ? 'Max altitude' : 'Difficulty' }}</span>{{ $tour->max_altitude ?: ($tour->difficulty ?: 'Easy') }}</td>
            </tr>
        </table>

        <h2>Overview</h2>
        <p>{{ $tour->summary }}</p>

        @if ($tour->itinerary)
            <h2>Day-by-day Itinerary</h2>
            @foreach ($tour->itinerary as $i => $day)
                <div class="day"><strong>Day {{ $i + 1 }}: {{ $day['title'] ?? '' }}</strong><br>{{ $day['description'] ?? '' }}</div>
            @endforeach
        @endif

        <table class="cols" width="100%"><tr>
            <td>
                <h2>Included</h2>
                <ul>@foreach ($tour->includes ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul>
            </td>
            <td>
                <h2>Not Included</h2>
                <ul>@foreach ($tour->excludes ?? [] as $item)<li>{{ $item }}</li>@endforeach</ul>
            </td>
        </tr></table>

        @if ($tour->visa_info)
            <h2>Visa Information</h2>
            <p>{{ $tour->visa_info }}</p>
        @endif

        <div class="footer">
            {{ site('name') }} · {{ site('address') }}<br>
            {{ site('phone') }} · {{ site('mobile') }} · {{ site('email') }} · {{ route('tours.show', $tour) }}
        </div>
    </div>
</body>
</html>
