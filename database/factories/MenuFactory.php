<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }

    public function withCategory(?Category $category = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'category_id' => $category ?? Category::factory()->createQuietly(),
        ]);
    }

    public function withClient(?Client $client = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'client_id' => $client ?? Client::factory()->createQuietly(),
        ]);
    }
}
