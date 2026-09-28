<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRentalPartnerIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isRentalPartner() && $user->vendor_status !== 'approved') {
            return redirect()->route('rental-partner.dashboard')
                ->with('status', 'Your rental partner account is still awaiting approval.');
        }

        return $next($request);
    }
}
