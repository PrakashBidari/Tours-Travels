<?php

namespace App\Livewire\Backend\Profile;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm as BaseDeleteUserForm;

class DeleteUserForm extends BaseDeleteUserForm
{
    public function render(): View
    {
        return view('backend.profile.delete-user-form');
    }
}
