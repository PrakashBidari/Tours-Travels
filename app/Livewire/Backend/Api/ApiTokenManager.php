<?php

namespace App\Livewire\Backend\Api;

use Illuminate\View\View;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager as BaseApiTokenManager;

class ApiTokenManager extends BaseApiTokenManager
{
    public function render(): View
    {
        return view('backend.api.api-token-manager');
    }
}
