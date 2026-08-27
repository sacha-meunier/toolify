<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /* This user is part of one seeded workspace. */
        $this->createUser('Member', 'member@toolify.com');

        /* This user is part of all seeded workspace. */
        $this->createUser('Admin', 'admin@toolify.com');

        /* This user is not part of any seeded workspace. */
        $this->createUser('Alone', 'alone@toolify.com');

        $this->createSchoolTeam('Dupont', 'dupont', 'Design Web 2526');
        $this->createSchoolTeam('Wera', 'wera', 'Design Web 2025-2026');
    }

    /**
     * Create an admin and a member pair for a school class : the admin owns a "HEPL" workspace
     * with a team inside it and the member is added to that team.
     */
    private function createSchoolTeam(string $name, string $slugPrefix, string $teamName): void
    {
        $admin = $this->createUser("{$name} Admin", "{$slugPrefix}.admin@toolify.com");
        $member = $this->createUser("{$name} Member", "{$slugPrefix}.member@toolify.com");

        $workspace = Workspace::query()->updateOrCreate(
            ['slug' => "hepl-{$slugPrefix}"],
            ['name' => 'HEPL', 'owner_id' => $admin->id]
        );

        if (! $workspace->invite_code) {
            $workspace->update(['invite_code' => Workspace::generateUniqueInviteCode()]);
        }

        $team = Team::query()->updateOrCreate(
            ['slug' => Str::slug($teamName), 'workspace_id' => $workspace->id],
            ['name' => $teamName]
        );

        $workspace->members()->syncWithoutDetaching($member);
        $team->members()->syncWithoutDetaching($member);
    }

    private function createUser(string $name, string $email): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make('password')],
        );

        $user->forceFill([
            'email_verified_at' => now(),
            'onboarded_at' => now(),
            'remember_token' => Str::random(10),
        ])->save();

        return $user;
    }
}
