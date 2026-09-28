<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class RentalPartnerLayout extends Component
{
    public function render(): View
    {
        return view('rental-partner.layouts.app');
    }
}
