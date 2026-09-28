<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** `admin.can:bookings` — super admins pass; staff pass only for modules their role grants. */
class EnsureAdminPermission
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! $request->user()?->canAccessAdmin($module)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
