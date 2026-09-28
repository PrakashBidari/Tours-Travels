@props(['light' => false, 'compact' => false])

{{-- Ram Tours & Travel wordmark: Himalayan peaks with a gold sun swoosh. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 64 48" class="{{ $compact ? 'h-9' : 'h-11 sm:h-12' }} w-auto shrink-0" aria-hidden="true">
        <defs>
            <linearGradient id="rl-peak" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="{{ $light ? '#ffffff' : '#00AEEF' }}" />
                <stop offset="1" stop-color="{{ $light ? '#bcd6ff' : '#0047AB' }}" />
            </linearGradient>
        </defs>
        <path d="M6 20a26 20 0 0 1 44-6" fill="none" stroke="#F4B400" stroke-width="3.2" stroke-linecap="round" />
        <path d="M2 44 L20 16 L27 26 L37 8 L62 44 Z" fill="url(#rl-peak)" />
        <path d="M37 8 L32 17 L35.5 15.5 L37.5 19 L40 14.5 L42.5 16 Z" fill="#fff" opacity=".95" />
        <path d="M20 16 L16.5 21.5 L19 20.5 L21.5 23 Z" fill="#fff" opacity=".9" />
        <path d="M4 44 C18 36 34 35 60 41" fill="none" stroke="#F4B400" stroke-width="2.4" stroke-linecap="round" />
    </svg>
    <span class="leading-none">
        <span class="block font-extrabold tracking-tight {{ $compact ? 'text-base' : 'text-lg sm:text-xl' }} {{ $light ? 'text-white' : 'text-brand-700' }}">RAM TOURS <span class="text-gold-500">&amp;</span> TRAVEL</span>
        <span class="mt-1 flex items-center gap-1.5 text-[10px] font-semibold tracking-[0.2em] {{ $light ? 'text-brand-100' : 'text-brand-900' }}">
            <span class="h-px w-4 bg-gold-500"></span>PVT. LTD.<span class="h-px w-4 bg-gold-500"></span>
        </span>
        @unless ($compact)
            <span class="mt-1 block text-[10px] italic {{ $light ? 'text-brand-200' : 'text-slate-500' }}">{{ __('Your Journey, Our Commitment') }}</span>
        @endunless
    </span>
</span>
