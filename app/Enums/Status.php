<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case EXPIRED = 'expired';

    public function display(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'danger',
            self::EXPIRED => 'warning',
        };
    }

    public function errorMessage(): string
    {
        return match ($this) {
            self::SUSPENDED => 'Your account is suspended',
            self::EXPIRED => 'Your organization has expired',
            default => ''
        };
    }
}
