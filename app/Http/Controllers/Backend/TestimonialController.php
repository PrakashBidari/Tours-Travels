<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    use HandlesAdminInput;

    public function index(): View
    {
        return view('backend.testimonials.index', [
            'testimonials' => Testimonial::orderBy('is_approved')->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('backend.testimonials.form', ['testimonial' => new Testimonial(['rating' => 5, 'source' => 'google', 'is_approved' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($this->validated($request));

        return redirect()->route('dashboard.testimonials.index')->with('status', 'Review added.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('backend.testimonials.form', ['testimonial' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->validated($request, $testimonial));

        return redirect()->route('dashboard.testimonials.index')->with('status', 'Review updated.');
    }

    public function toggle(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update(['is_approved' => ! $testimonial->is_approved]);

        return back()->with('status', $testimonial->is_approved ? 'Review approved and now visible.' : 'Review hidden.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return back()->with('status', 'Review deleted.');
    }

    protected function validated(Request $request, ?Testimonial $testimonial = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:2000'],
            'source' => ['required', Rule::in(array_keys(Testimonial::SOURCES))],
            'video_url' => ['nullable', 'url', 'max:500'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_approved' => ['boolean'],
            ...static::imageRules('avatar'),
        ]);

        $data['avatar'] = $this->singleImage($request, 'avatar', 'testimonials', $testimonial?->avatar);
        unset($data['avatar_url']);

        return $data;
    }
}
