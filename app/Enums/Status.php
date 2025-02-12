<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';

    public function display(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'danger',
        };
    }
}
