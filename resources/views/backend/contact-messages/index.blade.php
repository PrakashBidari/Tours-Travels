<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Contact Messages</h2>
    </x-slot>

    <div class="py-12">
        <div
            class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4"
            x-data="{
                viewModalOpen: false,
                viewName: '',
                viewEmail: '',
                viewPhone: '',
                viewDescription: '',
                viewDate: '',
                openViewModal(name, email, phone, description, date) {
                    this.viewName = name;
                    this.viewEmail = email;
                    this.viewPhone = phone;
                    this.viewDescription = description;
                    this.viewDate = date;
                    this.viewModalOpen = true;
                },
            }"
        >
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No contact messages yet." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Received</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($messages as $message)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $message->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $message->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $message->phone }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $message->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $message->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <button
                                            type="button"
                                            @click="openViewModal({{ Js::from($message->name) }}, {{ Js::from($message->email) }}, {{ Js::from($message->phone) }}, {{ Js::from($message->description) }}, {{ Js::from($message->created_at->format('M d, Y \a\t g:i A')) }})"
                                            class="text-xs font-medium text-indigo-600 hover:text-indigo-700"
                                        >View</button>
                                        <form method="POST" action="{{ route('dashboard.contact-messages.destroy', $message) }}" class="inline" data-confirm="Delete this message? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                x-show="viewModalOpen"
                x-cloak
                x-trap.inert.noscroll="viewModalOpen"
                @keydown.escape.window="viewModalOpen = false"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            >
                <div class="fixed inset-0 bg-gray-500/75" @click="viewModalOpen = false"></div>

                <div class="relative mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl sm:w-full sm:max-w-lg sm:mx-auto p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="viewName"></h3>
                    <p class="mt-1 text-sm text-gray-500" x-text="viewDate"></p>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex gap-2"><dt class="font-medium text-gray-500 w-16 shrink-0">Email</dt><dd class="text-gray-800 dark:text-gray-200" x-text="viewEmail"></dd></div>
                        <div class="flex gap-2"><dt class="font-medium text-gray-500 w-16 shrink-0">Phone</dt><dd class="text-gray-800 dark:text-gray-200" x-text="viewPhone"></dd></div>
                    </dl>

                    <p class="mt-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line" x-text="viewDescription"></p>

                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="viewModalOpen = false" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
