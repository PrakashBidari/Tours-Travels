<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/** Photo & video albums (the Instagram-style strip on the home page uses the first photos). */
class GalleryItemController extends Controller
{
    public function index(Request $request): View
    {
        return view('backend.gallery.index', [
            'items' => GalleryItem::ordered()->when($request->filled('album'), fn ($q) => $q->where('album', $request->string('album')))->get(),
            'albums' => GalleryItem::select('album')->distinct()->orderBy('album')->pluck('album'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'album' => ['required', 'string', 'max:60'],
            'title' => ['nullable', 'string', 'max:160'],
            'photos' => ['nullable', 'array', 'max:30'],
            'photos.*' => ['image', 'max:6144'],
            'url' => ['nullable', 'url', 'max:1000'],
            'type' => ['required', 'in:image,video'],
        ]);

        $created = 0;

        foreach ($request->file('photos', []) as $photo) {
            GalleryItem::create(['album' => $data['album'], 'title' => $data['title'], 'type' => 'image', 'path' => $photo->store('gallery', 'public')]);
            $created++;
        }

        if (! empty($data['url'])) {
            GalleryItem::create(['album' => $data['album'], 'title' => $data['title'], 'type' => $data['type'], 'path' => $data['url']]);
            $created++;
        }

        return back()->with($created ? 'status' : 'error', $created ? "{$created} item(s) added to \"{$data['album']}\"." : 'Choose photos to upload or paste an image / YouTube URL.');
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->update($request->validate([
            'title' => ['nullable', 'string', 'max:160'],
            'album' => ['required', 'string', 'max:60'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]));

        return back()->with('status', 'Gallery item updated.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if (! str_starts_with($galleryItem->path, 'http')) {
            Storage::disk('public')->delete($galleryItem->path);
        }

        $galleryItem->delete();

        return back()->with('status', 'Gallery item removed.');
    }
}
