<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'team_id' => Team::first(),
            'role' => Role::ADMINISTRATOR,
        ]);
    }
}
