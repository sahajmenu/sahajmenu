<?php

namespace App\Actions;

use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
use App\Services\StatusHistoryService;

class UnsuspendAction
{
    public function handle(User|Client $record, ?string $reason = null): void
    {
        resolve(StatusHistoryService::class)->create(
            record: $record,
            status: Status::ACTIVE,
            reason: $reason,
        );
    }
}
