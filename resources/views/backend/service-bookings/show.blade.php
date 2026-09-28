<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ $booking->service_label }} booking</p>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight"><span class="font-mono">{{ $booking->reference }}</span> · {{ $booking->title }}</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ $confirmationUrl }}" target="_blank" class="rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">Customer view ↗</a>
                <a href="{{ URL::signedRoute('booking.invoice', $booking) }}" class="rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">Invoice PDF</a>
                <a href="{{ route('dashboard.service-bookings.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">&larr; All bookings</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Traveler & trip</h3>
                        <div class="flex gap-2"><x-admin.status-badge :status="$booking->status" /><x-admin.status-badge :status="$booking->payment_status" /></div>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 p-6 text-sm">
                        <div><dt class="text-xs text-gray-500">Lead traveler</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->full_name }} @if ($booking->user)<span class="text-xs text-gray-400">(account #{{ $booking->user_id }})</span>@endif</dd></div>
                        <div><dt class="text-xs text-gray-500">Email</dt><dd><a href="mailto:{{ $booking->email }}" class="text-indigo-600">{{ $booking->email }}</a></dd></div>
                        <div><dt class="text-xs text-gray-500">Phone</dt><dd class="text-gray-900 dark:text-gray-100"><a href="tel:{{ $booking->phone }}">{{ $booking->phone }}</a> · <a href="https://wa.me/{{ preg_replace('/\D/', '', $booking->phone) }}" target="_blank" class="text-emerald-600">WhatsApp</a></dd></div>
                        <div><dt class="text-xs text-gray-500">Nationality / passport</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->nationality ?: '—' }} · {{ $booking->passport_number ?: '—' }}</dd></div>
                        <div><dt class="text-xs text-gray-500">Travel date</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->travel_date?->format('D, M d, Y') ?? '—' }}@if ($booking->return_date) → {{ $booking->return_date->format('D, M d, Y') }}@endif</dd></div>
                        <div><dt class="text-xs text-gray-500">Travelers</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->adults }} adult(s), {{ $booking->children }} child(ren)</dd></div>
                        @include('frontend.booking.partials.details', ['labelClass' => 'text-xs text-gray-500', 'valueClass' => 'text-gray-900 dark:text-gray-100'])
                        @if ($booking->special_request)
                            <div class="sm:col-span-2"><dt class="text-xs text-gray-500">Special request</dt><dd class="rounded-lg bg-amber-50 dark:bg-amber-500/10 p-3 text-gray-700 dark:text-gray-200">{{ $booking->special_request }}</dd></div>
                        @endif
                    </dl>
                </div>

                @if ($documents = $booking->details['documents'] ?? [])
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Uploaded documents</h3>
                        <ul class="mt-3 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                            @foreach ($documents as $i => $document)
                                <li class="flex items-center justify-between py-2"><span class="text-gray-700 dark:text-gray-200">{{ $document['name'] }}</span><a href="{{ route('dashboard.service-bookings.document', [$booking, $i]) }}" class="text-xs font-medium text-indigo-600">Download</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($booking->bookable)
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 text-sm">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Linked {{ Str::headline(class_basename($booking->bookable)) }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">{{ $booking->bookable->title ?? $booking->bookable->name ?? $booking->bookable->bus_name }}</p>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('dashboard.service-bookings.update', $booking) }}" class="space-y-4 rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 h-fit">
                @csrf @method('PUT')
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Manage booking</h3>
                <x-validation-errors />

                <div class="grid grid-cols-2 gap-3">
                    <x-admin.field name="status" label="Booking status" type="select" :value="$booking->status" :options="array_combine(config('travel.booking_statuses'), array_map('ucfirst', config('travel.booking_statuses')))" />
                    <x-admin.field name="payment_status" label="Payment" type="select" :value="$booking->payment_status" :options="array_combine(config('travel.payment_statuses'), array_map('ucfirst', config('travel.payment_statuses')))" />
                    <x-admin.field name="payment_method" label="Method" type="select" :value="$booking->payment_method" :options="['' => '—'] + collect(config('travel.payment_methods'))->map->label->all()" />
                    <x-admin.field name="transaction_id" label="Transaction ID" :value="$booking->transaction_id" />
                    @if (in_array($booking->service_type, ['flight', 'bus', 'tour'], true))
                        <x-admin.field name="pnr" label="PNR" :value="$booking->pnr" />
                        <x-admin.field name="ticket_number" label="Ticket / e-ticket no." :value="$booking->ticket_number" />
                    @endif
                    <x-admin.field name="subtotal" label="Subtotal (NPR)" type="number" step="0.01" min="0" :value="$booking->subtotal" :help="$booking->service_type === 'flight' ? 'Enter the quoted fare so the customer can pay online.' : null" />
                    <x-admin.field name="discount" label="Discount (NPR)" type="number" step="0.01" min="0" :value="$booking->discount" />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">Total: <strong class="text-gray-900 dark:text-gray-100">{{ npr($booking->total) }}</strong> @if ($booking->coupon_code)<span class="text-xs text-gray-400">(coupon {{ $booking->coupon_code }})</span>@endif @if ($booking->paid_at)<span class="text-xs text-emerald-600">· paid {{ $booking->paid_at->format('M d') }}</span>@endif</p>
                <x-admin.field name="admin_notes" label="Internal notes" type="textarea" rows="3" :value="$booking->admin_notes" />
                <x-admin.field name="notify_customer" label="Email & SMS the customer about this update" type="checkbox" :value="true" />
                <x-button class="w-full justify-center">Save changes</x-button>
            </form>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6 flex justify-end">
            <form method="POST" action="{{ route('dashboard.service-bookings.destroy', $booking) }}" data-confirm="Delete booking {{ $booking->reference }} permanently? This cannot be undone.">
                @csrf @method('DELETE')
                <button class="text-sm font-medium text-red-600 hover:text-red-700">Delete booking</button>
            </form>
        </div>
    </div>
</x-app-layout>
