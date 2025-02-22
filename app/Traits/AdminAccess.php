<?php

declare(strict_types=1);

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
