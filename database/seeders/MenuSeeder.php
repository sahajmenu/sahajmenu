<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::get()->each(function ($client) {
            Menu::factory()->count(5)->withClient($client)->withCategory()->createQuietly();
        });
    }
}
