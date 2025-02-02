<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Services\ClientService;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    /**
     * Creates a folder for client menu images
     */
    protected function afterCreate(): void
    {
        resolve(ClientService::class)->createDirectoryForClientMenuImages($this->record);
    }
}
