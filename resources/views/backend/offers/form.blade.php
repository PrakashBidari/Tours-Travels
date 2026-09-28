<x-admin.form-page :title="$offer->exists ? 'Edit offer' : 'New offer'" :back="route('dashboard.offers.index')"
    :action="$offer->exists ? route('dashboard.offers.update', $offer) : route('dashboard.offers.store')" :method="$offer->exists ? 'PUT' : 'POST'">
    <x-admin.card title="Offer banner" description="Shown on the Offers page and home page while active.">
        <x-admin.field name="title" label="Title" :value="$offer->title" required full />
        <x-admin.field name="badge" label="Badge" :value="$offer->badge" placeholder="Festival, Early Bird, New Year…" />
        <x-admin.field name="link_url" label="Button link" :value="$offer->link_url" placeholder="/tours?category=nepal" />
        <x-admin.field name="description" label="Description" type="textarea" rows="2" :value="$offer->description" />
        <x-admin.field name="image" label="Banner image" type="image" :value="$offer->image" full />
    </x-admin.card>
    <x-admin.card title="Coupon rules" description="Leave the code empty for a display-only deal.">
        <x-admin.field name="code" label="Coupon / promo code" :value="$offer->code" placeholder="DASHAIN15" />
        <x-admin.field name="applies_to" label="Applies to" type="select" :value="$offer->applies_to" :options="['all' => 'All services'] + config('travel.service_types')" />
        <x-admin.field name="discount_type" label="Discount type" type="select" :value="$offer->discount_type" :options="['percent' => 'Percentage (%)', 'fixed' => 'Fixed amount (NPR)']" />
        <x-admin.field name="discount_value" label="Discount value" type="number" step="0.01" min="0" :value="$offer->discount_value" required />
        <x-admin.field name="min_amount" label="Minimum booking amount (NPR)" type="number" step="0.01" min="0" :value="$offer->min_amount" />
        <x-admin.field name="max_discount" label="Maximum discount (NPR)" type="number" step="0.01" min="0" :value="$offer->max_discount" />
        <x-admin.field name="starts_at" label="Starts" type="datetime-local" :value="$offer->starts_at" />
        <x-admin.field name="expires_at" label="Expires (countdown timer)" type="datetime-local" :value="$offer->expires_at" />
        <x-admin.field name="usage_limit" label="Usage limit (optional)" type="number" min="1" :value="$offer->usage_limit" />
        <x-admin.field name="is_active" label="Active" type="checkbox" :value="$offer->is_active" />
    </x-admin.card>
</x-admin.form-page>
