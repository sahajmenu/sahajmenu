<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\Status;
use App\Models\Client;
use App\Models\User;
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
    protected static ?string $password;

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
            'remember_token' => Str::random(10)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function asSuperAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::SUPER_ADMIN,
        ]);
    }

    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::ADMIN,
        ]);
    }

    public function withClient(Role $role, Status $status = Status::ACTIVE): static
    {
        return $this->state(function () use ($role, $status) {
            $client = Client::factory()->createQuietly();
            $client->status()->create([
                'status' => $status,
            ]);
            return [
                'client_id' => $client->id,
                'role' => $role,
            ];
        });
    }

    public function withStageHistory(Status $status = Status::ACTIVE): static
    {
        return $this->afterCreating(function (User $user) use ($status) {
            $user->status()->create([
                'status' => $status,
            ]);
        });
    }
}
