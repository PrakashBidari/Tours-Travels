@props(['title' => null, 'subtitle' => null])

<div class="w-full">
    @if ($title)
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 ring-1 ring-gray-900/5 px-6 py-8 sm:px-10 sm:py-10">
        {{ $slot }}
    </div>
</div>
