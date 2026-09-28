<?php

namespace App\Livewire\Backend\Profile;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm as BaseTwoFactorAuthenticationForm;

class TwoFactorAuthenticationForm extends BaseTwoFactorAuthenticationForm
{
    public function render(): View
    {
        return view('backend.profile.two-factor-authentication-form');
    }
}
