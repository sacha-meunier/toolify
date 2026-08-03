<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tool;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
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
        $testUser = User::query()->where('email', 'member@toolify.com')->firstOrFail();

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
                    'github_url' => $tool['github_url'] ?? null,
                    'twitter_url' => $tool['twitter_url'] ?? null,
                    'app_store_url' => $tool['app_store_url'] ?? null,
                    'play_store_url' => $tool['play_store_url'] ?? null,
                    'logo_url' => isset($tool['logo_url']) ? Storage::disk('public')->url($tool['logo_url']) : null,
                    'banner_url' => isset($tool['banner_url']) ? Storage::disk('public')->url($tool['banner_url']) : null,
                    'gallery' => isset($tool['gallery']) ? array_map(fn (string $path): string => Storage::disk('public')->url($path), $tool['gallery']) : null,
                    'categories' => $tool['categories'],
                    'pricing' => $tool['pricing'],
                    'platforms' => $tool['platforms'],
                    'founded_year' => $tool['founded_year'] ?? null,
                    'first_release_year' => $tool['first_release_year'] ?? null,
                    'headquarters' => $tool['headquarters'] ?? null,
                    'headcount' => $tool['headcount'] ?? null,
                    'status' => $tool['status'] ?? 'active',
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
