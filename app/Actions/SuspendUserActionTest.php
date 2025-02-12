<?php

namespace App\Actions;

use App\Enums\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SuspendUserActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanSuspendUser(): void
    {
        $admin = User::factory()->asSuperAdmin()->createQuietly();

        $user = User::factory()->asAdmin()->createQuietly();

        $this->actingAs($admin);

        $this->assertEquals(Status::ACTIVE, $user->status);
        $this->assertEmpty($user->suspended_at);

        resolve(SuspendUserAction::class)->handle($user);

        $this->assertEquals(Status::SUSPENDED, $user->status);
        $this->assertNotEmpty($user->suspended_at);
    }
}
