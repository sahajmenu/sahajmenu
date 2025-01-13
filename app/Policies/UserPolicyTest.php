<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use DatabaseTransactions;

    public static function clientAccessUser(): array
    {
        return [
            [Role::OWNER],
            [Role::MANAGER],
        ];
    }

    public static function adminAccessUser(): array
    {
        return [
            [Role::SUPER_ADMIN],
            [Role::ADMIN],
        ];
    }

    #[Test]
    #[DataProvider('clientAccessUser')]
    public function clientUserCanAccess(Role $role): void
    {
        $user = User::factory()->withClient($role)->createQuietly();
        $newUser = User::factory()->createQuietly([
            'role' => Role::FRONT_DESK,
            'client_id' => $user->client->id,
        ]);

        $this->actingAs($user);

        $this->assertTrue($user->can('viewAny', User::class));
        $this->assertTrue($user->can('view', $newUser));
        $this->assertTrue($user->can('create', User::class));
        $this->assertTrue($user->can('update', $newUser));
        $this->assertTrue($user->cannot('delete', $newUser));
    }

    #[Test]
    #[DataProvider('adminAccessUser')]
    public function adminUserCanAccess(Role $role): void
    {
        $adminUser = User::factory()->createQuietly([
            'role' => $role,
        ]);

        $user = User::factory()->withClient(Role::OWNER)->createQuietly();

        $this->actingAs($adminUser);

        $this->assertTrue($adminUser->can('viewAny', User::class));
        $this->assertTrue($adminUser->can('view', $user));
        $this->assertTrue($adminUser->can('create', User::class));
        $this->assertTrue($adminUser->can('update', $user));
        $this->assertTrue($adminUser->can('delete', $user));
    }

    #[Test]
    #[DataProvider('clientAccessUser')]
    public function clientUserCannotViewAndEditUserOfDifferentClient(Role $role): void
    {
        $user = User::factory()->withClient($role)->createQuietly();
        $newUser = User::factory()->withClient($role)->createQuietly();

        $this->actingAs($user);

        $this->assertTrue($user->cannot('view', $newUser));
        $this->assertTrue($user->cannot('update', $newUser));
    }
}
