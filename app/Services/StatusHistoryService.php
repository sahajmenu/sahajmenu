<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Client;
use App\Models\StatusHistory;
use App\Models\User;

class StatusHistoryService
{
    public function create(User|Client $record, Status $status = Status::ACTIVE, ?string $reason = null): StatusHistory
    {
        return $record->status()->create([
            'reason' => $reason,
            'status' => $status
        ]);
    }
}
