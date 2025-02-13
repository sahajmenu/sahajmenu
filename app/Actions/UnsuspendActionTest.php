<?php

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UnsuspendActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanUnsuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStageHistory()->createQuietly();

        $user = User::factory()->asAdmin()->withStageHistory(Status::SUSPENDED)->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);

        resolve(UnsuspendAction::class)->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);
    }

    public function testAdminCanUnsuspendClientUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStageHistory()->createQuietly();

        $user = User::factory()->withClient(Role::OWNER)->withStageHistory(Status::SUSPENDED)->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);

        resolve(UnsuspendAction::class)->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);
    }
}
