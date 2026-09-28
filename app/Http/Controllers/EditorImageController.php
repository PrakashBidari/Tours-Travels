<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditorImageController extends Controller
{
    /**
     * Store an image uploaded from a CKEditor description field and return its
     * public URL, in the {url: ...} shape CKEditor's upload adapter expects.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->isVendor() || Auth::user()->isAdminUser(), 403);

        $request->validate([
            'upload' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('upload')->store('description-images', 'public');

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }
}
