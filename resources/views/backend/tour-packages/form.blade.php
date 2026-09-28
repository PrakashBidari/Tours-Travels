<x-admin.form-page :title="$tour->exists ? 'Edit: '.$tour->title : 'New Tour Package'" :back="route('dashboard.tour-packages.index')"
    :action="$tour->exists ? route('dashboard.tour-packages.update', $tour) : route('dashboard.tour-packages.store')" :method="$tour->exists ? 'PUT' : 'POST'"
    :submit="$tour->exists ? 'Save changes' : 'Create package'">

    <x-admin.card title="Basics">
        <x-admin.field name="title" label="Package title" :value="$tour->title" required full />
        <x-admin.field name="category" label="Category" type="select" :value="$tour->category" :options="config('travel.tour_categories')" required />
        <x-admin.field name="trip_style" label="Trip style" type="select" :value="$tour->trip_style" :options="['' => '—'] + config('travel.trip_styles')" />
        <x-admin.field name="destination" label="Destination" :value="$tour->destination" required help="e.g. Pokhara, Everest Region, Dubai — used for the destination filter." />
        <x-admin.field name="country" label="Country" :value="$tour->country ?? 'Nepal'" required />
        <x-admin.field name="duration_days" label="Days" type="number" min="1" :value="$tour->duration_days ?? 1" required />
        <x-admin.field name="duration_nights" label="Nights" type="number" min="0" :value="$tour->duration_nights ?? 0" required />
        <x-admin.field name="season" label="Best season" type="select" :value="$tour->season" :options="config('travel.seasons')" />
        <x-admin.field name="group_size" label="Group size" :value="$tour->group_size" />
        <x-admin.field name="summary" label="Short summary (cards & SEO)" type="textarea" rows="2" :value="$tour->summary" />
    </x-admin.card>

    <x-admin.card title="Pricing & visibility">
        <x-admin.field name="price" label="Price per person (NPR)" type="number" step="0.01" min="0" :value="$tour->price" required />
        <x-admin.field name="sale_price" label="Offer price (optional)" type="number" step="0.01" min="0" :value="$tour->sale_price" help="Shown with the original price struck through." />
        <x-admin.field name="rating" label="Rating (0–5)" type="number" step="0.1" min="0" :value="$tour->rating" />
        <x-admin.field name="review_count" label="Review count" type="number" min="0" :value="$tour->review_count ?? 0" />
        <x-admin.field name="position" label="Sort position (optional)" type="number" min="0" :value="$tour->position" help="Lower numbers show first." />
        <div class="flex flex-col gap-1">
            <x-admin.field name="is_active" label="Published on website" type="checkbox" :value="$tour->is_active" />
            <x-admin.field name="is_featured" label="Feature on home page (Hot Deals)" type="checkbox" :value="$tour->is_featured" />
        </div>
    </x-admin.card>

    <x-admin.card title="Trip details">
        <x-admin.field name="hotel" label="Hotel / accommodation" :value="$tour->hotel" />
        <x-admin.field name="meals" label="Meals" :value="$tour->meals" />
        <x-admin.field name="transport" label="Transport" :value="$tour->transport" />
        <x-admin.field name="difficulty" label="Difficulty (treks)" :value="$tour->difficulty" />
        <x-admin.field name="max_altitude" label="Max altitude (treks)" :value="$tour->max_altitude" />
        <x-admin.field name="map_embed_url" label="Google Maps embed URL" type="url" :value="$tour->map_embed_url" />
        <x-admin.field name="overview" label="Overview" type="richtext" :value="$tour->overview" />
        <x-admin.field name="highlights" label="Highlights" type="lines" :value="$tour->highlights" />
        <x-admin.field name="itinerary" label="Itinerary — one day per line: Title | Description" type="lines" rows="8"
            :value="collect($tour->itinerary ?? [])->map(fn ($d) => ($d['title'] ?? '').' | '.($d['description'] ?? ''))->all()"
            placeholder="Arrival in Kathmandu | Airport pickup and hotel transfer…" />
        <x-admin.field name="includes" label="Included" type="lines" :value="$tour->includes" />
        <x-admin.field name="excludes" label="Not included" type="lines" :value="$tour->excludes" />
        <x-admin.field name="visa_info" label="Visa information (international)" type="textarea" rows="3" :value="$tour->visa_info" />
    </x-admin.card>

    <x-admin.card title="Gallery" description="First image is the cover. Upload photos or paste image URLs.">
        <x-admin.field name="images" type="images" :value="$tour->images" />
    </x-admin.card>

    <x-admin.card title="SEO">
        <x-admin.field name="meta_title" label="Meta title" :value="$tour->meta_title" full />
        <x-admin.field name="meta_description" label="Meta description" type="textarea" rows="2" :value="$tour->meta_description" />
        @if ($tour->exists)
            <x-admin.field name="regenerate_slug" label="Regenerate URL from title (current: /tours/{{ $tour->slug }})" type="checkbox" :value="false" />
        @endif
    </x-admin.card>
</x-admin.form-page>
