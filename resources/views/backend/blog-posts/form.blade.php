<x-admin.form-page :title="$post->exists ? 'Edit post' : 'New blog post'" :back="route('dashboard.blog-posts.index')"
    :action="$post->exists ? route('dashboard.blog-posts.update', $post) : route('dashboard.blog-posts.store')" :method="$post->exists ? 'PUT' : 'POST'">
    <x-admin.card title="Post">
        <x-admin.field name="title" label="Title" :value="$post->title" required full />
        <x-admin.field name="category" label="Category" type="select" :value="$post->category" :options="array_combine(\App\Models\BlogPost::CATEGORIES, \App\Models\BlogPost::CATEGORIES)" />
        <x-admin.field name="status" label="Status" type="select" :value="$post->exists && ! $post->published_at ? 'draft' : 'publish'" :options="['publish' => 'Publish / schedule', 'draft' => 'Draft']" />
        <x-admin.field name="published_at" label="Publish date & time" type="datetime-local" :value="$post->published_at" help="A future date schedules the post." />
        <x-admin.field name="excerpt" label="Excerpt" type="textarea" rows="2" :value="$post->excerpt" />
        <x-admin.field name="content" label="Content" type="richtext" rows="14" :value="$post->content" />
        <x-admin.field name="featured_image" label="Featured image" type="image" :value="$post->featured_image" full />
    </x-admin.card>
    <x-admin.card title="SEO">
        <x-admin.field name="meta_title" label="Meta title" :value="$post->meta_title" full />
        <x-admin.field name="meta_description" label="Meta description" type="textarea" rows="2" :value="$post->meta_description" />
    </x-admin.card>
</x-admin.form-page>
