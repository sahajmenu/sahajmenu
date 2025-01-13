<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientPolicyTest extends TestCase
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

        $this->assertTrue($user->can('viewAny', Client::class));
        $this->assertTrue($user->can('view', $user->client));
        $this->assertTrue($user->cannot('create', Client::class));
        $this->assertTrue($user->can('update', $user->client));
        $this->assertTrue($user->cannot('delete', $user->client));
    }

    #[Test]
    #[DataProvider('adminAccessUser')]
    public function adminUserCanAccess(Role $role): void
    {
        $user = User::factory()->createQuietly([
            'role' => $role,
        ]);

        $client = Client::factory()->createQuietly();

        $this->assertTrue($user->can('viewAny', Client::class));
        $this->assertTrue($user->can('view', $client));
        $this->assertTrue($user->can('create', Client::class));
        $this->assertTrue($user->can('update', $client));
        $this->assertTrue($user->can('delete', $client));
    }

    #[Test]
    public function frontDeskCannotAccess(): void
    {
        $user = User::factory()->withClient(Role::FRONT_DESK)->createQuietly();
        $this->assertTrue($user->cannot('viewAny', Client::class));
        $this->assertTrue($user->cannot('view', $user->client));
        $this->assertTrue($user->cannot('create', Client::class));
        $this->assertTrue($user->cannot('update', $user->client));
        $this->assertTrue($user->cannot('delete', $user->client));
    }

    #[Test]
    #[DataProvider('clientAccessUser')]
    public function clientUserCannotEditViewDifferentClient(Role $role): void
    {
        $user = User::factory()->withClient($role)->createQuietly();
        $client = Client::factory()->createQuietly();

        $this->actingAs($user);
        $this->assertTrue($user->cannot('view', $client));
        $this->assertTrue($user->cannot('update', $client));
    }
}
