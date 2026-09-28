@props(['status'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset '.\App\Models\ServiceBooking::badgeClasses((string) $status)]) }}>{{ Str::headline((string) $status) }}</span>
