<x-admin.form-page :title="$faq->exists ? 'Edit question' : 'New question'" :back="route('dashboard.faqs.index')" width="max-w-3xl" :files="false"
    :action="$faq->exists ? route('dashboard.faqs.update', $faq) : route('dashboard.faqs.store')" :method="$faq->exists ? 'PUT' : 'POST'">
    <x-admin.card>
        <x-admin.field name="category" label="Category" type="select" :value="$faq->category" :options="array_combine(\App\Models\Faq::CATEGORIES, \App\Models\Faq::CATEGORIES)" />
        <x-admin.field name="position" label="Sort position" type="number" min="0" :value="$faq->position" />
        <x-admin.field name="question" label="Question" :value="$faq->question" required full />
        <x-admin.field name="answer" label="Answer" type="textarea" rows="5" :value="$faq->answer" required />
        <x-admin.field name="is_active" label="Show on website" type="checkbox" :value="$faq->is_active" />
    </x-admin.card>
</x-admin.form-page>
