{{-- Service-specific booking details as label => value rows (shared by the page, invoice and admin). --}}
@php
    $labels = [
        'category' => __('Package type'), 'destination' => __('Destination'), 'duration' => __('Duration'),
        'hotel' => __('Hotel'), 'meals' => __('Meals'), 'transport' => __('Transport'),
        'scope' => __('Flight type'), 'trip_type' => __('Trip type'), 'from' => __('From'), 'to' => __('To'),
        'cabin' => __('Cabin'), 'infants' => __('Infants'), 'preferred_airline' => __('Preferred airline'),
        'multi_city' => __('Other legs'), 'promo_code' => __('Promo code'),
        'operator' => __('Operator'), 'bus' => __('Bus'), 'seats' => __('Seats'), 'departure' => __('Departure'),
        'arrival' => __('Arrival'), 'boarding_point' => __('Boarding point'), 'dropping_point' => __('Dropping point'),
        'vehicle' => __('Vehicle'), 'pickup_location' => __('Pickup'), 'dropoff_location' => __('Drop-off'),
        'pickup_time' => __('Pickup time'), 'drive_mode' => __('Drive option'), 'service' => __('Purpose'),
        'days' => __('Days'), 'license_number' => __('Licence no.'),
        'country' => __('Country'), 'visa_type' => __('Visa type'), 'processing_time' => __('Processing time'),
    ];
    $rows = collect($booking->details ?? [])
        ->except('documents')
        ->filter(fn ($value) => filled($value))
        ->mapWithKeys(fn ($value, $key) => [$labels[$key] ?? Str::headline($key) => is_array($value) ? implode(', ', $value) : $value]);
@endphp

@foreach ($rows as $label => $value)
    <div class="{{ $rowClass ?? '' }}">
        <dt class="{{ $labelClass ?? '' }}">{{ $label }}</dt>
        <dd class="{{ $valueClass ?? '' }}">{{ $value }}</dd>
    </div>
@endforeach
