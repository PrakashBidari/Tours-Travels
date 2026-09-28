<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isVendor() && $user->vendor_status !== 'approved') {
            return redirect()->route('vendor.dashboard')
                ->with('status', 'Your vendor account is still awaiting approval.');
        }

        return $next($request);
    }
}
