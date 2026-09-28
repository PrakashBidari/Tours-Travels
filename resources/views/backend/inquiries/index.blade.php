<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Inquiries @if ($newCount)<span class="ml-2 rounded-full bg-rose-500 px-2 py-0.5 text-xs text-white">{{ $newCount }} new</span>@endif</h2>
            <a href="{{ route('dashboard.contact-messages.index') }}" class="text-sm font-medium text-indigo-600">Contact form messages ({{ $contactCount }}) &rarr;</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.inquiries.index') }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ ! request('type') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">All</a>
                @foreach (config('travel.inquiry_types') as $key => $label)
                    <a href="{{ route('dashboard.inquiries.index', ['type' => $key]) }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('type') === $key ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No inquiries yet." class="min-w-full">
                        <thead>
                            <tr><th>Received</th><th>From</th><th data-filter data-filter-label="Type">Type</th><th>Subject</th><th data-filter data-filter-label="Status">Status</th><th data-no-sort></th></tr>
                        </thead>
                        <tbody>
                            @foreach ($inquiries as $inquiry)
                                <tr class="{{ $inquiry->status === 'new' ? 'font-semibold' : '' }}">
                                    <td data-order="{{ $inquiry->created_at->timestamp }}" class="whitespace-nowrap">{{ $inquiry->created_at->format('M d, H:i') }}</td>
                                    <td>{{ $inquiry->name }}<div class="text-xs font-normal text-gray-500">{{ $inquiry->phone ?: $inquiry->email }}</div></td>
                                    <td>{{ $inquiry->type_label }}</td>
                                    <td>{{ Str::limit($inquiry->subject ?: $inquiry->message, 60) }}</td>
                                    <td><x-admin.status-badge :status="$inquiry->status" /></td>
                                    <td class="text-right"><a href="{{ route('dashboard.inquiries.show', $inquiry) }}" class="text-xs font-medium text-indigo-600">Open</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
