<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use App\Services\StatusHistoryService;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        ];
    }

    public function withUser(Role $role, Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (Client $client) use ($role, $status) {
            $user = User::factory()->createQuietly([
                'client_id' => $client->id,
                'role' => $role,
            ]);

            resolve(StatusHistoryService::class)->create(
                record: $user,
                status: $status,
            );
        });
    }

    public function withMenuImageFolder(): static
    {
        return $this->afterCreating(function (Client $client) {
            resolve(ClientService::class)->createDirectoryForClientMenuImages($client);
        });
    }

    public function withStageHistory(Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (Client $client) use ($status) {
            resolve(StatusHistoryService::class)->create(
                record: $client,
                status: $status
            );
        });
    }
}
