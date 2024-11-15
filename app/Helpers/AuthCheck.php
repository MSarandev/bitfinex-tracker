<?php

namespace App\Helpers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

class AuthCheck
{
    /**
     * @return Authenticatable|null
     */
    public static function checkUser(): ?Authenticatable
    {
        if (Auth::check() === false || Auth::user() === null) {
            Log::error('User is not authenticated');

            throw new UnauthorizedException();
        }

        return Auth::user();
    }
}
