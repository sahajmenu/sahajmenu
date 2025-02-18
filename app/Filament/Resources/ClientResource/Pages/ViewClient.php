<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\StatusHistoryResource\RelationManagers\StatusRelationManager;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    public function getRelationManagers(): array
    {
        return [
            StatusRelationManager::class
        ];
    }
}
