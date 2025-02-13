<?php

namespace App\Traits;

trait HasActiveIcon
{
    public static function getActiveNavigationIcon(): ?string
    {
        return str(self::getNavigationIcon())
            ->replace('heroicon-o-', 'heroicon-s-')
            ->toString();
    }
}
