<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Rental Partners') }}</h2>
            <a href="{{ route('dashboard.rental-partners.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Rental Partner</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex gap-2">
                @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <a href="{{ route('dashboard.rental-partners.index', $value ? ['status' => $value] : []) }}"
                        class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('status', '') === $value ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No rental partners found." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rental Partner</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" data-filter data-filter-label="Status">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($rentalPartners as $rentalPartner)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 text-sm">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $rentalPartner->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $rentalPartner->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $rentalPartner->company_name }}</td>
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400' => $rentalPartner->vendor_status === 'approved',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' => $rentalPartner->vendor_status === 'pending',
                                            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' => $rentalPartner->vendor_status === 'rejected',
                                        ])>{{ ucfirst($rentalPartner->vendor_status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        @if ($rentalPartner->vendor_status !== 'approved')
                                            <form method="POST" action="{{ route('dashboard.rental-partners.approve', $rentalPartner) }}" class="inline">
                                                @csrf
                                                <button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button>
                                            </form>
                                        @endif
                                        @if ($rentalPartner->vendor_status !== 'rejected')
                                            <form method="POST" action="{{ route('dashboard.rental-partners.reject', $rentalPartner) }}" class="inline">
                                                @csrf
                                                <button class="text-xs font-medium text-red-600 hover:text-red-700">Reject</button>
                                            </form>
                                        @endif
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
