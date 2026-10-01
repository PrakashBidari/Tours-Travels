@props(['light' => false])

{{-- Ram Tours & Travel mark (the peaks from <x-ram-logo>); `light` is for dark backgrounds. --}}
@php $gradient = $light ? 'am-peak-light' : 'am-peak'; @endphp
<svg viewBox="0 0 64 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" {{ $attributes }}>
    <defs>
        <linearGradient id="{{ $gradient }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="{{ $light ? '#ffffff' : '#00AEEF' }}" />
            <stop offset="1" stop-color="{{ $light ? '#bcd6ff' : '#0047AB' }}" />
        </linearGradient>
    </defs>
    <path d="M6 20a26 20 0 0 1 44-6" stroke="#F4B400" stroke-width="3.2" stroke-linecap="round" />
    <path d="M2 44 L20 16 L27 26 L37 8 L62 44 Z" fill="url(#{{ $gradient }})" />
    <path d="M37 8 L32 17 L35.5 15.5 L37.5 19 L40 14.5 L42.5 16 Z" fill="{{ $light ? '#0047AB' : '#fff' }}" opacity=".95" />
    <path d="M20 16 L16.5 21.5 L19 20.5 L21.5 23 Z" fill="{{ $light ? '#0047AB' : '#fff' }}" opacity=".9" />
    <path d="M4 44 C18 36 34 35 60 41" stroke="#F4B400" stroke-width="2.4" stroke-linecap="round" />
</svg>
