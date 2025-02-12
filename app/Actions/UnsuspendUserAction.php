<?php

namespace App\Actions;

use App\Enums\Status;
use App\Models\Client;
use App\Models\User;

class UnsuspendUserAction
{
    public function handle(User|Client $record): void
    {
        $record->update([
            'suspended_at' => null,
            'status' => Status::ACTIVE
        ]);
    }
}
