<?php

namespace Database\Seeders;

use App\Models\User;
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
    }

    private function createUser(string $name, string $email): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
            'onboarded_at' => now(),
            'remember_token' => Str::random(10),
        ])->save();

        return $user;
    }
}
