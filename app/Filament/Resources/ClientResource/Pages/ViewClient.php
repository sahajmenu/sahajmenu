<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\RelationManagers\ClientPaymentsRelationManager;
use App\Filament\Resources\ClientResource\RelationManagers\StatusHistoryRelationManager;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    public function getRelationManagers(): array
    {
        return [
            StatusHistoryRelationManager::class,
            ClientPaymentsRelationManager::class,
        ];
    }
}
