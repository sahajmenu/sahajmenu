<?php

namespace Tests\Unit;

use App\Enums\Role;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use DatabaseTransactions;

    public static function clientUser(): array
    {
        return [
            [Role::OWNER],
            [Role::MANAGER],
            [Role::FRONT_DESK],
        ];
    }

    public static function adminUser(): array
    {
        return [
            [Role::SUPER_ADMIN],
            [Role::ADMIN],
        ];
    }

    #[Test]
    #[DataProvider('clientUser')]
    public function clientUserCannotAccessFilamentUserResource(Role $role): void
    {
        $user = User::factory()->withClient($role)->createQuietly();

        $this->actingAs($user)
            ->get(UserResource::getUrl('index'))
            ->assertStatus(403);
    }

    #[Test]
    #[DataProvider('adminUser')]
    public function adminUserCanAccessFilamentUserResource(Role $role): void
    {
        $user = User::factory()->withClient($role)->createQuietly();

        $this->actingAs($user)
            ->get(UserResource::getUrl('index'))
            ->assertStatus(200);
    }
}
