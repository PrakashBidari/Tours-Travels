<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Staff & Roles</h2>
            <a href="{{ route('dashboard.staff.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Staff Member</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach (config('travel.staff_roles') as $role)
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $role['label'] }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ implode(', ', array_map('ucfirst', $role['modules'])) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Since</th><th class="px-4 py-3"></th></tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($staff as $member)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $member->email }}</td>
                                <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $member->isSuperAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">{{ $member->isSuperAdmin() ? 'Super Admin' : $member->staff_role_label }}</span></td>
                                <td class="px-4 py-3 text-gray-500">{{ $member->created_at->format('M Y') }}</td>
                                <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                    @if ($member->isStaff())
                                        <a href="{{ route('dashboard.staff.edit', $member) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.staff.destroy', $member) }}" class="inline" data-confirm="Remove {{ $member->name }}'s staff account?">@csrf @method('DELETE')<button class="text-xs font-medium text-red-600">Remove</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
