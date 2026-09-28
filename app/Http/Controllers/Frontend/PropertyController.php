<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function show(Property $property): View
    {
        abort_unless($property->status === 'approved', 404);

        $isWishlisted = Auth::check() && Auth::user()->isUser()
            && Auth::user()->wishlists()->where('property_id', $property->id)->exists();

        $popularPackages = Property::approved()->popular()
            ->where('id', '!=', $property->id)
            ->ordered()
            ->take(4)
            ->get();

        return view('frontend.properties.show', [
            'property' => $property,
            'isWishlisted' => $isWishlisted,
            'popularPackages' => $popularPackages,
        ]);
    }
}
