<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\PageSetting;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('frontend.contact', [
            'settings' => PageSetting::current(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::create($data);

        rescue(fn () => User::adminsFor('inquiries')
            ->each(fn (User $admin) => $admin->notify(new NewContactMessageNotification($message))));

        return back()->with('status', "Thanks, {$message->name}! We've received your message and will get back to you soon.");
    }
}
