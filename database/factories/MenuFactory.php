<?php

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
            'category_id' => Category::factory()->createQuietly(),
            'client_id' => Client::factory()->createQuietly(),
        ];
    }

    public function withCategory(Category $category): static
    {
        return $this->state(function (array $attributes) use ($category) {
            return ['category_id' => $category->id];
        });
    }

    public function withClient(Client $client): static
    {
        return $this->state(function (array $attributes) use ($client) {
            return ['client_id' => $client->id];
        });
    }
}
