<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class RoleBasedLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        /** @var Request $request */
        $user = $request->user();

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($user->dashboardUrl());
    }
}
