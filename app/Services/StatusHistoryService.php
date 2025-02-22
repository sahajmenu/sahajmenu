<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Status;
use App\Models\Client;
use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StatusHistoryService
{
    public function create(User|Client $record, Status $status = Status::ACTIVE, ?string $reason = null): StatusHistory
    {
        $actionedBy = Auth::user()->id ?? null;

        return $record->status()->create([
            'reason' => $reason,
            'status' => $status,
            'actioned_by' => $actionedBy
        ]);
    }
}
