<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Offers, Coupons & Promo Codes</h2>
            <a href="{{ route('dashboard.offers.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Offer</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No offers yet." class="min-w-full">
                        <thead><tr><th data-no-sort>Banner</th><th>Offer</th><th>Code</th><th>Discount</th><th data-filter data-filter-label="Applies to">Applies to</th><th>Valid until</th><th>Used</th><th data-filter data-filter-label="Status">Status</th><th data-no-sort></th></tr></thead>
                        <tbody>
                            @foreach ($offers as $offer)
                                @php $live = $offer->is_active && (! $offer->expires_at || $offer->expires_at->isFuture()) && (! $offer->starts_at || $offer->starts_at->isPast()); @endphp
                                <tr>
                                    <td><img src="{{ $offer->image_url }}" alt="" class="h-10 w-16 rounded object-cover"></td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $offer->title }}@if ($offer->badge)<div class="text-xs font-normal text-gray-500">{{ $offer->badge }}</div>@endif</td>
                                    <td class="font-mono">{{ $offer->code ?: '—' }}</td>
                                    <td>{{ $offer->discount_label }}</td>
                                    <td>{{ $offer->applies_to === 'all' ? 'All services' : config('travel.service_types.'.$offer->applies_to) }}</td>
                                    <td data-order="{{ $offer->expires_at?->timestamp ?? PHP_INT_MAX }}">{{ $offer->expires_at?->format('M d, Y') ?? 'No expiry' }}</td>
                                    <td>{{ $offer->used_count }}{{ $offer->usage_limit ? ' / '.$offer->usage_limit : '' }}</td>
                                    <td><x-admin.status-badge :status="$live ? 'active' : ($offer->is_active ? 'expired' : 'inactive')" /></td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('dashboard.offers.edit', $offer) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.offers.destroy', $offer) }}" class="inline" data-confirm="Delete this offer?">@csrf @method('DELETE')<button class="text-xs font-medium text-red-600">Delete</button></form>
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
