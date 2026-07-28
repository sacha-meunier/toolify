<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\Platform;
use App\Enums\Pricing;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tool>
 */
class ToolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'team_id' => Team::factory(),
            'name' => $name,
            'slug' => str($name)->slug(),
            'tagline' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'website_url' => fake()->url(),
            'logo_url' => null,
            'categories' => [Category::Business->value],
            'pricing' => Pricing::Free->value,
            'platforms' => [Platform::Web->value],
        ];
    }
}
