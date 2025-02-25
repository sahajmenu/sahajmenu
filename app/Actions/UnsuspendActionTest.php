<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UnsuspendActionTest extends TestCase
{
    use DatabaseTransactions;

    private UnsuspendAction $unsuspendAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unsuspendAction = resolve(UnsuspendAction::class);
    }

    #[Test]
    public function adminCanUnsuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStatusHistory()->createQuietly();

        $user = User::factory()->asAdmin()->withStatusHistory(Status::SUSPENDED)->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);

        $this->unsuspendAction->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);
    }

    #[Test]
    public function adminCanUnsuspendClientUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->withStatusHistory()->createQuietly();

        $user = User::factory()->withClient(Role::OWNER)->withStatusHistory(Status::SUSPENDED)->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::SUSPENDED, $user->latestStatus->status);

        $this->unsuspendAction->handle(record: $user);

        $user->refresh();

        $this->assertEquals(Status::ACTIVE, $user->latestStatus->status);
    }
}
