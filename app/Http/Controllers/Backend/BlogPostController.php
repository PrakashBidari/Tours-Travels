<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    use HandlesAdminInput;

    public function index(): View
    {
        return view('backend.blog-posts.index', ['posts' => BlogPost::with('author')->latest()->get()]);
    }

    public function create(): View
    {
        return view('backend.blog-posts.form', ['post' => new BlogPost(['published_at' => now(), 'category' => 'Travel Tips'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(BlogPost::class, $data['title']);
        $data['user_id'] = Auth::id();

        BlogPost::create($data);

        return redirect()->route('dashboard.blog-posts.index')->with('status', 'Post saved.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('backend.blog-posts.form', ['post' => $blogPost]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update($this->validated($request, $blogPost));

        return redirect()->route('dashboard.blog-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return back()->with('status', 'Post deleted.');
    }

    protected function validated(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:60'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,publish'],
            'published_at' => ['nullable', 'date'],
            ...static::imageRules('featured_image'),
        ]);

        $data['content'] = clean($data['content']);
        $data['featured_image'] = $this->singleImage($request, 'featured_image', 'blog', $post?->featured_image);
        // "Publish" with a future date schedules the post; drafts have no publish date.
        $data['published_at'] = $data['status'] === 'publish' ? ($data['published_at'] ?? now()) : null;
        unset($data['featured_image_url'], $data['status']);

        return $data;
    }
}
