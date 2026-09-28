<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('backend.faqs.index', ['faqs' => Faq::ordered()->get()]);
    }

    public function create(): View
    {
        return view('backend.faqs.form', ['faq' => new Faq(['is_active' => true, 'category' => 'General'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validated($request));

        return redirect()->route('dashboard.faqs.index')->with('status', 'FAQ added.');
    }

    public function edit(Faq $faq): View
    {
        return view('backend.faqs.form', ['faq' => $faq]);
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return redirect()->route('dashboard.faqs.index')->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('status', 'FAQ deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', 'string', 'max:60'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }
}
