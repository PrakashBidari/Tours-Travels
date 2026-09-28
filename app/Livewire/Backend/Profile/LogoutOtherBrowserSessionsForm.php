<?php

namespace App\Livewire\Backend\Profile;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm as BaseLogoutOtherBrowserSessionsForm;

class LogoutOtherBrowserSessionsForm extends BaseLogoutOtherBrowserSessionsForm
{
    public function render(): View
    {
        return view('backend.profile.logout-other-browser-sessions-form');
    }
}
