<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /* This user is part of one seeded workspace. */
        User::factory()->create([
            'name' => 'Member',
            'email' => 'member@toolify.com',
        ]);

        /* This user is part of all seeded workspace. */
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@toolify.com',
        ]);

        /* This user is not part of any seeded workspace. */
        User::factory()->create([
            'name' => 'Alone',
            'email' => 'alone@toolify.com',
        ]);
    }
}
