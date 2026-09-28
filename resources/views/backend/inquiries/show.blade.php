<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $inquiry->type_label }} — {{ $inquiry->name }}</h2>
            <a href="{{ route('dashboard.inquiries.index') }}" class="text-sm text-gray-500">&larr; All inquiries</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 space-y-4 text-sm">
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-xs text-gray-500">Name</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $inquiry->name }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Received</dt><dd class="text-gray-900 dark:text-gray-100">{{ $inquiry->created_at->format('D, M d Y H:i') }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Email</dt><dd><a href="mailto:{{ $inquiry->email }}?subject={{ rawurlencode('Re: '.($inquiry->subject ?: $inquiry->type_label)) }}" class="text-indigo-600">{{ $inquiry->email }}</a></dd></div>
                    <div><dt class="text-xs text-gray-500">Phone</dt><dd class="text-gray-900 dark:text-gray-100">@if ($inquiry->phone)<a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a> · <a href="https://wa.me/{{ preg_replace('/\D/', '', $inquiry->phone) }}" target="_blank" class="text-emerald-600">WhatsApp</a>@else — @endif</dd></div>
                    @foreach ($inquiry->data ?? [] as $key => $value)
                        <div><dt class="text-xs text-gray-500">{{ Str::headline($key) }}</dt><dd class="font-mono text-gray-900 dark:text-gray-100">{{ $value }}</dd></div>
                    @endforeach
                </dl>
                @if ($inquiry->subject)<p class="font-semibold text-gray-900 dark:text-gray-100">{{ $inquiry->subject }}</p>@endif
                <div class="whitespace-pre-line rounded-lg bg-gray-50 dark:bg-gray-900/40 p-4 text-gray-700 dark:text-gray-200">{{ $inquiry->message ?: '—' }}</div>
            </div>

            <div class="space-y-4">
                <form method="POST" action="{{ route('dashboard.inquiries.update', $inquiry) }}" class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 space-y-4">
                    @csrf @method('PUT')
                    <x-admin.field name="status" label="Status" type="select" :value="$inquiry->status" :options="['new' => 'New', 'in_progress' => 'In progress', 'resolved' => 'Resolved']" />
                    <x-admin.field name="admin_notes" label="Notes" type="textarea" rows="4" :value="$inquiry->admin_notes" />
                    <x-button class="w-full justify-center">Save</x-button>
                </form>
                <form method="POST" action="{{ route('dashboard.inquiries.destroy', $inquiry) }}" data-confirm="Delete this inquiry?" class="text-right">
                    @csrf @method('DELETE')
                    <button class="text-sm font-medium text-red-600">Delete inquiry</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
