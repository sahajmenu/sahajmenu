<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    /**
     * Creates a folder for client menu images
     */
    protected function afterCreate(): void
    {
        $client = $this->record;

        Storage::disk('menus')
            ->makeDirectory("{$client->id}-{$client->subdomain}");

    }
}
