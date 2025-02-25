<?php

declare(strict_types=1);

namespace Tests\Feature\RelationManagers;

use App\Enums\Role;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\RelationManagers\UsersRelationManager;
use App\Models\User;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UsersRelationManagerTest extends TestCase
{
    use DatabaseTransactions;

    public static function canAddRole(): array
    {
        return [
            [Role::MANAGER],
            [Role::FRONT_DESK],
        ];
    }

    public static function cannotAddRole(): array
    {
        return [
            [Role::SUPER_ADMIN],
            [Role::ADMIN],
        ];
    }

    #[DataProvider('canAddRole')]
    public function test_client_can_add_user(Role $role): void
    {
        $user = User::factory()->withClient(Role::OWNER)->createQuietly();

        Livewire::actingAs($user)
            ->test(UsersRelationManager::class, [
                'ownerRecord' => $user->client,
                'pageClass' => EditClient::class,
            ])
            ->assertTableActionExists('create')
            ->mountTableAction(CreateAction::class)
            ->setTableActionData([
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => $role,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors()
            ->assertSuccessful();
    }

    #[DataProvider('cannotAddRole')]
    public function test_client_cannot_add_user(Role $role): void
    {
        $user = User::factory()->withClient(Role::OWNER)->createQuietly();

        Livewire::actingAs($user)
            ->test(UsersRelationManager::class, [
                'ownerRecord' => $user->client,
                'pageClass' => EditClient::class,
            ])
            ->assertTableActionExists('create')
            ->mountTableAction(CreateAction::class)
            ->setTableActionData([
                'name' => 'Test',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => $role,
            ])
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['role'])
            ->assertSuccessful();
    }
}
