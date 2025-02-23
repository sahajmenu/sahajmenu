<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\RelationManagers\TablesRelationManager;
use App\Filament\Resources\ClientResource\RelationManagers\UsersRelationManager;
use App\Traits\AdminAccess;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    use AdminAccess;

    protected static string $resource = ClientResource::class;

    public function getBreadcrumbs(): array
    {
        if ($this->hasAdminAccess()) {
            return [
                ClientResource::getUrl() => 'Clients',
                'Edit',
            ];
        }

        return [];
    }

    public function getRelationManagers(): array
    {
        return [
            UsersRelationManager::class,
            TablesRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
