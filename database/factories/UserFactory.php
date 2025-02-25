<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
use App\Services\StatusHistoryService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    public function asSuperAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::SUPER_ADMIN,
        ]);
    }

    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => Role::ADMIN,
        ]);
    }

    public function withClient(Role $role, Status $status = Status::ACTIVE): static
    {
        return $this->state(function () use ($role, $status): array {
            $client = Client::factory()->createQuietly();

            resolve(StatusHistoryService::class)->create(
                record: $client,
                status: $status
            );

            return [
                'client_id' => $client->id,
                'role' => $role,
            ];
        });
    }

    public function withStatusHistory(Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (User $user) use ($status): void {
            resolve(StatusHistoryService::class)->create(
                record: $user,
                status: $status,
            );
        });
    }
}
