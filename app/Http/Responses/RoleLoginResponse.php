<?php

namespace App\Http\Responses;

use App\Services\LoginAccounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;

class RoleLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        $destination = app(LoginAccounts::class)->destination($request->user()?->role);
        if (! $destination) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Your account needs an assigned role. Please contact your administrator.']);
        }

        // A saved account role chooses the destination, never a form or query parameter.
        $request->session()->forget('url.intended');

        return $request->wantsJson() ? response()->json(['redirect' => $destination]) : redirect($destination);
    }
}
