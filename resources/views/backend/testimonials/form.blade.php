<x-admin.form-page :title="$testimonial->exists ? 'Edit review' : 'Add review'" :back="route('dashboard.testimonials.index')" width="max-w-3xl"
    :action="$testimonial->exists ? route('dashboard.testimonials.update', $testimonial) : route('dashboard.testimonials.store')" :method="$testimonial->exists ? 'PUT' : 'POST'">
    <x-admin.card>
        <x-admin.field name="name" label="Customer name" :value="$testimonial->name" required />
        <x-admin.field name="location" label="City / country" :value="$testimonial->location" />
        <x-admin.field name="rating" label="Rating" type="select" :value="$testimonial->rating" :options="[5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★']" />
        <x-admin.field name="source" label="Source" type="select" :value="$testimonial->source" :options="\App\Models\Testimonial::SOURCES" />
        <x-admin.field name="content" label="Review" type="textarea" rows="5" :value="$testimonial->content" required />
        <x-admin.field name="video_url" label="Video review URL (YouTube)" type="url" :value="$testimonial->video_url" />
        <x-admin.field name="position" label="Sort position" type="number" min="0" :value="$testimonial->position" />
        <x-admin.field name="avatar" label="Photo" type="image" :value="$testimonial->avatar" full />
        <x-admin.field name="is_approved" label="Approved (visible on website)" type="checkbox" :value="$testimonial->is_approved" />
    </x-admin.card>
</x-admin.form-page>
