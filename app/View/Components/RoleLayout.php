<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class RoleLayout extends Component
{
    public function render(): View
    {
        $view = match (true) {
            Auth::user()?->isVendor() => 'vendor.layouts.app',
            Auth::user()?->isUser() => 'account.layouts.app',
            default => 'backend.layouts.app',
        };

        return view($view);
    }
}
