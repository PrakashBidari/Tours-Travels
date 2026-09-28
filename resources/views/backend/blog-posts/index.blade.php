<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Travel Blog</h2>
            <a href="{{ route('dashboard.blog-posts.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Post</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No posts yet." class="min-w-full">
                        <thead><tr><th data-no-sort>Image</th><th>Title</th><th data-filter data-filter-label="Category">Category</th><th data-filter data-filter-label="Status">Status</th><th>Publish date</th><th>Views</th><th data-no-sort></th></tr></thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td><img src="{{ $post->image_url }}" alt="" class="h-10 w-14 rounded object-cover"></td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $post->title }}<div class="text-xs font-normal text-gray-500">{{ $post->author?->name }}</div></td>
                                    <td>{{ $post->category }}</td>
                                    <td><x-admin.status-badge :status="match ($post->status_label) { 'Published' => 'confirmed', 'Scheduled' => 'processing', default => 'draft' }" class="!capitalize" />
                                        <span class="sr-only">{{ $post->status_label }}</span></td>
                                    <td data-order="{{ $post->published_at?->timestamp ?? 0 }}">{{ $post->published_at?->format('M d, Y H:i') ?? '—' }}</td>
                                    <td>{{ number_format($post->views) }}</td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        @if ($post->status_label === 'Published')<a href="{{ route('blog.show', $post) }}" target="_blank" class="text-xs text-gray-500">View</a>@endif
                                        <a href="{{ route('dashboard.blog-posts.edit', $post) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.blog-posts.destroy', $post) }}" class="inline" data-confirm="Delete this post?">@csrf @method('DELETE')<button class="text-xs font-medium text-red-600">Delete</button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
