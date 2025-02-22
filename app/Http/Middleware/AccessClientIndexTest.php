<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Filament\Resources\ClientResource;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessClientIndexTest extends TestCase
{
    use DatabaseTransactions;

    public static function adminAccessUser(): array
    {
        return [
            [Role::SUPER_ADMIN],
            [Role::ADMIN],
        ];
    }

    public static function clientAccessUser(): array
    {
        return [
            [Role::OWNER],
            [Role::MANAGER],
            [Role::FRONT_DESK],
        ];
    }

    #[Test]
    #[DataProvider('adminAccessUser')]
    public function userCanAccessClientIndex(Role $role): void
    {
        $user = User::factory()->createQuietly([
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->get(ClientResource::getUrl('index'))
            ->assertOk();
    }

    #[Test]
    #[DataProvider('clientAccessUser')]
    public function userCannotAccessClientIndex(Role $role): void
    {
        $user = User::factory()->createQuietly([
            'role' => $role,
        ]);

        $this->actingAs($user)
            ->get(ClientResource::getUrl('index'))
            ->assertForbidden();
    }
}
