<?php

declare(strict_types=1);

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
