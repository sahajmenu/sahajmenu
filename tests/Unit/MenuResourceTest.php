<?php

namespace Tests\Unit;

use App\Enums\Role;
use App\Filament\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Resources\MenuResource\Pages\EditMenu;
use App\Models\Category;
use App\Models\Client;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MenuResourceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It runs as we inject client id in CreateMenu.
     */
    #[Test]
    public function clientCanCreateMenuWithoutPassingClientId(): void
    {
        Storage::fake('menus');

        $user = User::factory()->withClient(Role::OWNER)->createQuietly();
        $category = Category::factory()->createQuietly([
            'client_id' => $user->client->id,
        ]);

        $this->actingAs($user);

        $data = [
            'name' => 'Chicken Momo',
            'price' => 22,
            'category_id' => $category->id,
            'images' => UploadedFile::fake()->create('image.jpeg'),
        ];

        Livewire::test(CreateMenu::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Menu::class, [
            'name' => $data['name'],
            'price' => $data['price'],
            'category_id' => $category->id,
            'client_id' => $user->client->id,
        ]);

        $menu = Menu::first();

        Storage::disk('menus')->assertExists($menu->image);

    }

    #[Test]
    public function adminNeedsToPassClientId(): void
    {
        Storage::fake('menus');

        $user = User::factory()->asAdmin()->createQuietly();
        $client = Client::factory()->createQuietly();
        $category = Category::factory()->createQuietly([
            'client_id' => $client->id,
        ]);

        $this->actingAs($user);

        $data = [
            'name' => 'Chicken Momo',
            'price' => 22,
            'category_id' => $category->id,
            'client_id' => $client->id,
            'images' => ['image.jpeg'],
        ];

        Livewire::test(CreateMenu::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Menu::class, [
            'name' => $data['name'],
            'price' => $data['price'],
            'category_id' => $category->id,
            'client_id' => $client->id,
        ]);

        $menu = Menu::first();

        Storage::disk('menus')->assertExists($menu->image);
    }

    #[Test]
    public function clientCanEditTheirMenuOnly(): void
    {
        $user = User::factory()->withClient(Role::OWNER)->createQuietly();
        $menu = Menu::factory()->withClient($user->client)->withCategory()->createQuietly();

        $secondMenu = Menu::factory()->withClient()->withCategory()->createQuietly();

        Livewire::actingAs($user)
            ->test(EditMenu::class, ['record' => $menu->id])
            ->assertOk();

        $response = $this->actingAs($user)
            ->get(route('filament.admin.resources.menus.edit', [
                'record' => $secondMenu->id,
            ]));

        $response->assertNotFound();
    }

    #[Test]
    public function adminCanEditAllMenu(): void
    {
        $user = User::factory()->asAdmin()->createQuietly();
        $menu = Menu::factory()->withClient()->withCategory()->createQuietly();

        $secondMenu = Menu::factory()->withClient()->withCategory()->createQuietly();

        Livewire::actingAs($user)
            ->test(EditMenu::class, ['record' => $menu->id])
            ->assertOk();

        $response = $this->actingAs($user)
            ->get(route('filament.admin.resources.menus.edit', [
                'record' => $secondMenu->id,
            ]));

        $response->assertOk();
    }
}
