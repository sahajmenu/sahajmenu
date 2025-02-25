<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SuspendActionTest extends TestCase
{
    use DatabaseTransactions;

    private SuspendAction $suspendAction;

    public function setUp(): void
    {
        parent::setUp();
        $this->suspendAction = resolve(SuspendAction::class);
    }

    public function testAdminCanSuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStatusHistory()->createQuietly();

        $user = User::factory()->asAdmin()->withStatusHistory()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);

        $this->suspendAction->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);
    }

    public function testAdminCanSuspendClientUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStatusHistory()->createQuietly();
        $user = User::factory()->withClient(Role::OWNER)->withStatusHistory()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);

        $this->suspendAction->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);
    }
}
