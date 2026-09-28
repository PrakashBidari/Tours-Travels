<?php

namespace App\Livewire\Backend\Profile;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as BaseUpdateProfileInformationForm;

class UpdateProfileInformationForm extends BaseUpdateProfileInformationForm
{
    public function render(): View
    {
        return view('backend.profile.update-profile-information-form');
    }
}
