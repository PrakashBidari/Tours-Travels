<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Testimonials & Reviews</h2>
            <a href="{{ route('dashboard.testimonials.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ Add Review</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($testimonials as $review)
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 {{ $review->is_approved ? 'ring-gray-900/5 dark:ring-white/10' : 'ring-amber-300' }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $review->avatar_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $review->name }}</p>
                            <p class="text-xs text-gray-500">{{ $review->location }} · {{ \App\Models\Testimonial::SOURCES[$review->source] ?? $review->source }} · {{ str_repeat('★', $review->rating) }}</p>
                        </div>
                        <x-admin.status-badge :status="$review->is_approved ? 'approved' : 'pending'" />
                    </div>
                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-4">{{ $review->content }}</p>
                    <div class="mt-4 flex items-center gap-3 border-t border-gray-100 dark:border-gray-700 pt-3 text-xs font-medium">
                        <form method="POST" action="{{ route('dashboard.testimonials.toggle', $review) }}">@csrf<button class="{{ $review->is_approved ? 'text-amber-600' : 'text-emerald-600' }}">{{ $review->is_approved ? 'Hide' : 'Approve' }}</button></form>
                        <a href="{{ route('dashboard.testimonials.edit', $review) }}" class="text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('dashboard.testimonials.destroy', $review) }}" data-confirm="Delete this review?" class="ml-auto">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 py-12">No reviews yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
