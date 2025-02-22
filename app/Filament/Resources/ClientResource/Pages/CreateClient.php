<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Services\ClientService;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $days = (int) $data['days'] ?? 14;
        $data['expires_at'] = now()->addDays($days);
        return $data;
    }

    /**
     * Creates a folder for client menu images
     */
    protected function afterCreate(): void
    {
        resolve(ClientService::class)->createDirectoryForClientMenuImages($this->record);
    }
}
