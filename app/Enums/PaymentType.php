<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case BANK_TRANSFER = 'bank_transfer';
    case ESEWA = 'esewa';
    case CASH = 'cash';
    case SYSTEM = 'system';

    public function getLabel(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bank Transfer',
            self::ESEWA => 'Esewa',
            self::CASH => 'Cash',
            self::SYSTEM => 'System',
        };
    }
}
