<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Actions\CreateStatusHistory;
use App\Enums\Plan;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
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
            'plan' => Plan::FREE,
            'expires_at' => now()->addDays(14),
        ];
    }

    public function withUser(Role $role, Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (Client $client) use ($role, $status): void {
            $user = User::factory()->createQuietly([
                'client_id' => $client->id,
                'role' => $role,
            ]);

            resolve(CreateStatusHistory::class)->handle(
                record: $user,
                status: $status,
            );
        });
    }

    public function withMenuImageFolder(): static
    {
        return $this->afterCreating(function (Client $client): void {
            resolve(ClientService::class)->createDirectoryForClientMenuImages($client);
        });
    }

    public function withStatusHistory(Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (Client $client) use ($status): void {
            resolve(CreateStatusHistory::class)->handle(
                record: $client,
                status: $status
            );
        });
    }
}
