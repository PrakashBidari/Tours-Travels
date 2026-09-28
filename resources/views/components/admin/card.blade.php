@props(['title' => null, 'description' => null])

<section {{ $attributes->merge(['class' => 'rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10']) }}>
    @if ($title)
        <div class="border-b border-gray-100 px-6 py-4 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
            @if ($description)<p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>@endif
        </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-6">
        {{ $slot }}
    </div>
</section>
