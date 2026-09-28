<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Newsletter Subscribers ({{ $subscribers->count() }})</h2>
            <a href="{{ route('dashboard.subscribers.export') }}" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No subscribers yet." class="min-w-full">
                        <thead><tr><th>Email</th><th>Subscribed</th><th data-no-sort></th></tr></thead>
                        <tbody>
                            @foreach ($subscribers as $subscriber)
                                <tr>
                                    <td class="text-gray-900 dark:text-gray-100">{{ $subscriber->email }}</td>
                                    <td data-order="{{ $subscriber->created_at->timestamp }}">{{ $subscriber->created_at->format('M d, Y') }}</td>
                                    <td class="text-right"><form method="POST" action="{{ route('dashboard.subscribers.destroy', $subscriber) }}" data-confirm="Remove {{ $subscriber->email }}?">@csrf @method('DELETE')<button class="text-xs font-medium text-red-600">Remove</button></form></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
