<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait AdminAccess
{
    public static function hasAdminAccess(): bool
    {
        $user = Auth::user();

        return $user->adminAccess();
    }
}
