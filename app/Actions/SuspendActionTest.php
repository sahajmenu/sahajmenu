<?php

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SuspendActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanSuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStageHistory()->createQuietly();

        $user = User::factory()->asAdmin()->withStageHistory()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);

        resolve(SuspendAction::class)->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);
    }

    public function testAdminCanSuspendClientUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStageHistory()->createQuietly();
        $user = User::factory()->withClient(Role::OWNER)->withStageHistory()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);

        resolve(SuspendAction::class)->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);
    }
}
