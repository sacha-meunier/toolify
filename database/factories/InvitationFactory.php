<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'team_id' => null,
            'invited_by_id' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'status' => InvitationStatus::Pending,
        ];
    }

    /**
     * Scope the invitation to a specific team, inheriting its parent workspace.
     */
    public function forTeam(Team $team): static
    {
        return $this->state(fn () => [
            'workspace_id' => $team->workspace_id,
            'team_id' => $team->id,
        ]);
    }
}
