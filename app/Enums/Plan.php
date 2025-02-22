<?php

declare(strict_types=1);

namespace App\Enums;

enum Plan: string
{
    case FREE = 'free';
    case PAID = 'paid';

    public function display(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::PAID => 'Paid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FREE => 'primary',
            self::PAID => 'success',
        };
    }
}
