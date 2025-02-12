<?php

namespace App\Actions;

use App\Enums\Status;
use App\Models\User;

class UnsuspendUserAction
{
    public function handle(User $user): void
    {
        $user->update([
            'suspended_at' => null,
            'status' => Status::ACTIVE
        ]);
    }
}
