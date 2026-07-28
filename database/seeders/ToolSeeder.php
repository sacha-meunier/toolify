<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tool;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ToolSeeder extends Seeder
{
    /**
     * The seeder creates `Workspaces` that belongs to `admin@toolify.com`,
     * Each `Workspace`has one or many `Teams`
     * Each `Team` has one `Tool`
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@toolify.com')->firstOrFail();
        $testUser = User::query()->where('email', 'test@example.com')->firstOrFail();

        $tools = json_decode(file_get_contents(database_path('data/tools.json')), true);

        foreach ($tools as $index => $tool) {
            $toolSlug = Str::slug($tool['name']);

            $workspace = Workspace::query()->updateOrCreate(
                ['slug' => Str::slug($tool['company'])],
                ['name' => $tool['company'], 'owner_id' => $admin->id]
            );

            $team = Team::query()->updateOrCreate(
                ['slug' => $toolSlug, 'workspace_id' => $workspace->id],
                ['name' => $tool['name']]
            );

            Tool::query()->updateOrCreate(
                ['slug' => $toolSlug],
                [
                    'team_id' => $team->id,
                    'name' => $tool['name'],
                    'tagline' => $tool['tagline'],
                    'description' => $tool['description'],
                    'website_url' => $tool['website_url'],
                    'logo_url' => $tool['logo_url'] ?? null,
                    'categories' => $tool['categories'],
                    'pricing' => $tool['pricing'],
                    'platforms' => $tool['platforms'],
                ]
            );

            /* Give the test account membership in the first seeded workspace/team, so its stacks can be tried out locally. */
            if ($index === 0) {
                $workspace->members()->syncWithoutDetaching($testUser);
                $team->members()->syncWithoutDetaching($testUser);
            }
        }
    }
}
