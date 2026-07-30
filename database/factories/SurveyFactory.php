<?php

namespace Database\Factories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'query' => fake()->word(),
            'filters' => [
                'pricing' => [],
                'categories' => [],
                'platforms' => [],
            ],
            'last_visited_at' => null,
        ];
    }

    /**
     * Set the given owner (User/Workspace/Team instance) for this survey.
     */
    public function forOwner(Model $owner): static
    {
        return $this->state([
            'owner_type' => $owner::class,
            'owner_id' => $owner->getKey(),
        ]);
    }
}
