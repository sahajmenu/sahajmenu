<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class ClientService
{
    public function createDirectoryForClientMenuImages(Client $client): void
    {
        Storage::disk('menus')
            ->makeDirectory($client->id);
    }
}
