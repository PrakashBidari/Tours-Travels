<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">FAQ</h2>
            <a href="{{ route('dashboard.faqs.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Question</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @forelse ($faqs->groupBy('category') as $category => $items)
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="border-b border-gray-100 dark:border-gray-700 px-6 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $category }}</h3>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($items as $faq)
                            <li class="flex items-start gap-4 px-6 py-4 text-sm">
                                <span class="w-8 shrink-0 text-gray-400">{{ $faq->position }}</span>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $faq->question }} @unless ($faq->is_active)<span class="ml-1 text-xs text-amber-600">(hidden)</span>@endunless</p>
                                    <p class="mt-1 text-gray-500 dark:text-gray-400 line-clamp-2">{{ $faq->answer }}</p>
                                </div>
                                <a href="{{ route('dashboard.faqs.edit', $faq) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                <form method="POST" action="{{ route('dashboard.faqs.destroy', $faq) }}" data-confirm="Delete this question?">@csrf @method('DELETE')<button class="text-xs font-medium text-red-600">Delete</button></form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-center text-gray-400 py-12">No FAQs yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
