<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Quote, flight (PNR / fare / reissue / refund), visa and bus inquiries. */
class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        return view('backend.inquiries.index', [
            'inquiries' => Inquiry::query()
                ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
                ->latest()
                ->get(),
            'newCount' => Inquiry::where('status', 'new')->count(),
            'contactCount' => ContactMessage::count(),
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        if ($inquiry->status === 'new') {
            $inquiry->update(['status' => 'in_progress']);
        }

        return view('backend.inquiries.show', ['inquiry' => $inquiry]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $inquiry->update($request->validate([
            'status' => ['required', Rule::in(Inquiry::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]));

        return back()->with('status', 'Inquiry updated.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('dashboard.inquiries.index')->with('status', 'Inquiry deleted.');
    }
}
