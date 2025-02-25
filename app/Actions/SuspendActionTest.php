<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuspendActionTest extends TestCase
{
    use DatabaseTransactions;

    private SuspendAction $suspendAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->suspendAction = resolve(SuspendAction::class);
    }

    #[Test]
    public function adminCanSuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStatusHistory()->createQuietly();

        $user = User::factory()->asAdmin()->withStatusHistory()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);

        $this->suspendAction->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);
    }

    #[Test]
    public function adminCanSuspendClientUser(): void
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
