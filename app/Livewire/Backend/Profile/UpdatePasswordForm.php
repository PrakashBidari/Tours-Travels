<?php

namespace App\Livewire\Backend\Profile;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm as BaseUpdatePasswordForm;

class UpdatePasswordForm extends BaseUpdatePasswordForm
{
    public function render(): View
    {
        return view('backend.profile.update-password-form');
    }
}
