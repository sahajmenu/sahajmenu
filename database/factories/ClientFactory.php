<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'address' => $this->faker->address(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'subdomain' => lcfirst($name),
            'slug' => Str::slug($name),
        ];
    }

    public function withUser(Role $role): static
    {
        return $this->afterCreating(function (Client $client) use ($role) {
            User::factory()->createQuietly([
                'client_id' => $client->id,
                'role' => $role,
            ]);
        });
    }

    public function withMenuImageFolder(): static
    {
        return $this->afterCreating(function (Client $client) {
            resolve(ClientService::class)->createDirectoryForClientMenuImages($client);
        });
    }
}
