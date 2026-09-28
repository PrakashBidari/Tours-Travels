<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\VisaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VisaServiceController extends Controller
{
    use HandlesAdminInput;

    public function index(): View
    {
        return view('backend.visa-services.index', [
            'visas' => VisaService::withCount('bookings')->orderBy('country')->get(),
        ]);
    }

    public function create(): View
    {
        return view('backend.visa-services.form', ['visa' => new VisaService(['is_active' => true, 'visa_type' => 'tourist'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(VisaService::class, $data['title']);

        VisaService::create($data);

        return redirect()->route('dashboard.visa-services.index')->with('status', "\"{$data['title']}\" created.");
    }

    public function edit(VisaService $visaService): View
    {
        return view('backend.visa-services.form', ['visa' => $visaService]);
    }

    public function update(Request $request, VisaService $visaService): RedirectResponse
    {
        $visaService->update($this->validated($request, $visaService));

        return redirect()->route('dashboard.visa-services.index')->with('status', "\"{$visaService->title}\" updated.");
    }

    public function destroy(VisaService $visaService): RedirectResponse
    {
        $visaService->delete();

        return back()->with('status', "\"{$visaService->title}\" deleted.");
    }

    protected function validated(Request $request, ?VisaService $visa = null): array
    {
        $data = $request->validate([
            'country' => ['required', 'string', 'max:120'],
            'flag' => ['nullable', 'string', 'max:16'],
            'visa_type' => ['required', Rule::in(array_keys(config('travel.visa_types')))],
            'title' => ['required', 'string', 'max:190'],
            'processing_time' => ['nullable', 'string', 'max:120'],
            'validity' => ['nullable', 'string', 'max:120'],
            'stay_duration' => ['nullable', 'string', 'max:120'],
            'embassy_fee' => ['required', 'numeric', 'min:0'],
            'service_charge' => ['required', 'numeric', 'min:0'],
            'requirements' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            ...static::imageRules('image'),
        ]);

        $data['requirements'] = $this->lines($data['requirements'] ?? null);
        $data['description'] = filled($data['description'] ?? null) ? clean($data['description']) : null;
        $data['image'] = $this->singleImage($request, 'image', 'visas', $visa?->image);
        unset($data['image_url']);

        return $data;
    }
}
