<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()
            ->withStageHistory()
            ->withUser(Role::OWNER)
            ->withUser(Role::MANAGER)
            ->withUser(Role::FRONT_DESK)
            ->withMenuImageFolder()
            ->createQuietly([
                'name' => 'Sherpa Cafe',
                'address' => 'Boudha',
                'subdomain' => 'sherpa',
                'phone' => '9842819652',
                'slug' => 'sherpa-cafe',
            ]);

        Client::factory()
            ->withStageHistory()
            ->withUser(Role::OWNER)
            ->withUser(Role::MANAGER)
            ->withUser(Role::FRONT_DESK)
            ->withMenuImageFolder()
            ->createQuietly([
                'name' => 'Lama Cafe',
                'address' => 'Tinchuli',
                'subdomain' => 'lama',
                'phone' => '9842819672',
                'slug' => 'lama-cafe',
            ]);

    }
}
