<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Status;
use App\Models\Client;
use App\Models\User;

class SuspendAction
{
    public function handle(User|Client $record, ?string $reason = null): void
    {
        resolve(CreateStatusHistory::class)->handle(
            record: $record,
            status: Status::SUSPENDED,
            reason: $reason,
        );
    }
}
