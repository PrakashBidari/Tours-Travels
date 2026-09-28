<x-admin.form-page :title="$visa->exists ? 'Edit: '.$visa->title : 'New Visa Service'" :back="route('dashboard.visa-services.index')"
    :action="$visa->exists ? route('dashboard.visa-services.update', $visa) : route('dashboard.visa-services.store')" :method="$visa->exists ? 'PUT' : 'POST'"
    :submit="$visa->exists ? 'Save changes' : 'Create visa service'">

    <x-admin.card title="Visa">
        <x-admin.field name="title" label="Title" :value="$visa->title" required full help="e.g. United Arab Emirates Tourist Visa" />
        <x-admin.field name="country" label="Country" :value="$visa->country" required />
        <x-admin.field name="flag" label="Flag emoji" :value="$visa->flag" placeholder="🇦🇪" />
        <x-admin.field name="visa_type" label="Visa type" type="select" :value="$visa->visa_type" :options="config('travel.visa_types')" />
        <x-admin.field name="processing_time" label="Processing time" :value="$visa->processing_time" />
        <x-admin.field name="validity" label="Validity" :value="$visa->validity" />
        <x-admin.field name="stay_duration" label="Stay duration" :value="$visa->stay_duration" />
        <x-admin.field name="embassy_fee" label="Embassy fee (NPR)" type="number" step="0.01" min="0" :value="$visa->embassy_fee ?? 0" required />
        <x-admin.field name="service_charge" label="Service charge (NPR)" type="number" step="0.01" min="0" :value="$visa->service_charge ?? 0" required />
        <x-admin.field name="is_active" label="Show on website" type="checkbox" :value="$visa->is_active" />
        <x-admin.field name="is_featured" label="Featured" type="checkbox" :value="$visa->is_featured" />
    </x-admin.card>

    <x-admin.card title="Details">
        <x-admin.field name="requirements" label="Document checklist" type="lines" rows="8" :value="$visa->requirements" />
        <x-admin.field name="description" label="Description" type="richtext" :value="$visa->description" />
        <x-admin.field name="image" label="Cover image" type="image" :value="$visa->image" full />
    </x-admin.card>
</x-admin.form-page>
