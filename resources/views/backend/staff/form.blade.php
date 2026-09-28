<x-admin.form-page :title="$member->exists ? 'Edit '.$member->name : 'New staff member'" :back="route('dashboard.staff.index')" width="max-w-3xl" :files="false"
    :action="$member->exists ? route('dashboard.staff.update', $member) : route('dashboard.staff.store')" :method="$member->exists ? 'PUT' : 'POST'">
    <x-admin.card>
        <x-admin.field name="name" label="Full name" :value="$member->name" required />
        @if ($member->exists)
            <div><span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</span><p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $member->email }}</p></div>
        @else
            <x-admin.field name="email" label="Email (login)" type="email" :value="$member->email" required />
        @endif
        <x-admin.field name="phone" label="Phone" :value="$member->phone" />
        <x-admin.field name="staff_role" label="Role" type="select" :value="$member->staff_role" :options="collect(config('travel.staff_roles'))->map->label->all()" />
        <x-admin.field name="password" label="{{ $member->exists ? 'New password (leave blank to keep)' : 'Password' }}" type="password" :required="! $member->exists" />
        <x-admin.field name="password_confirmation" label="Confirm password" type="password" :required="! $member->exists" />
    </x-admin.card>
</x-admin.form-page>
