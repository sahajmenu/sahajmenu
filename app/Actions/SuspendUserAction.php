<?php

namespace App\Actions;

use App\Enums\Status;
use App\Models\User;

class SuspendUserAction
{
    public function handle(User $user): void
    {
        $user->update([
            'suspended_at' => now(),
            'status' => Status::SUSPENDED
        ]);
    }
}
